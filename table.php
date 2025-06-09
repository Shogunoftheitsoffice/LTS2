<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <title>Textbooks</title>
</head>
<body>

<?php
$servername = "localhost";
$username = "tyler";
$password = "Beagle02!";
$dbname = "LTS";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if barcode and TUID are set and not empty
if(isset($_POST['barcode']) && !empty($_POST['barcode']) && isset($_POST['tuid']) && !empty($_POST['tuid'])) {
    $barcode = $_POST['barcode'];
    $tuid = $_POST['tuid'];

    // Update checkedout field to 'yes', set last_checkout to current timestamp, and expected_return to two hours later for the corresponding entry based on barcode
    $sql = "UPDATE textbooks SET checkedout='yes', `last checkout`=NOW(), `expected return`=DATE_ADD(NOW(), INTERVAL 2 HOUR), TUID='$tuid' WHERE Barcode='$barcode'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Check if return button is clicked
if(isset($_POST['return_barcode']) && !empty($_POST['return_barcode'])) {
    $return_barcode = $_POST['return_barcode'];

    // Update checkedout field to 'no' and expected_return to NULL for the corresponding entry based on barcode
    $sql = "UPDATE textbooks SET checkedout='no', `expected return`=NULL, TUID=NULL WHERE Barcode='$return_barcode'";
    if ($conn->query($sql) === TRUE) {
        echo "Book returned successfully";
    } else {
        echo "Error returning book: " . $conn->error;
    }
}

// Check if checkbox is clicked
if(isset($_POST['selected_barcode']) && isset($_POST['checked'])) {
    $selected_barcode = $_POST['selected_barcode'];
    $checked = $_POST['checked']; // Get the value of the checkbox (1 for checked, 0 for unchecked)

    // Add or remove the selected barcode from the "selected" table based on checkbox status
    if($checked == 1) {
        $sql = "INSERT INTO selected (barcode) VALUES ('$selected_barcode')";
    } else {
        $sql = "DELETE FROM selected WHERE barcode='$selected_barcode'";
    }
    if ($conn->query($sql) === TRUE) {
        echo "Selected barcode updated successfully";
    } else {
        echo "Error updating selected barcode: " . $conn->error;
    }
}

$sql = "SELECT TUID, Course, `course title`, `book title`, Name, CheckedOut, `Last Checkout`, `Expected Return`, Barcode, Book FROM textbooks ORDER BY CheckedOut DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sql_count = "SELECT COUNT(*) AS total_entries FROM textbooks";
    $result_count = $conn->query($sql_count);
    $row_count = $result_count->fetch_assoc();
    $total_entries = $row_count['total_entries'];

    echo "<form id='textbooksForm' method='post' class='mb-3'>";
    echo "<label for='tuid-input'>TUID:</label>"; // Added labels for accessibility
    echo "<input type='text' id='tuid-input' name='tuid' required class='mr-2'>";
    echo "<label for='barcode-input'>Item Barcode:</label>"; // Added labels
    echo "<input type='text' id='barcode-input' name='barcode' required class='mr-2'>";
    echo "<input type='submit' value='Submit'>";
    echo "&nbsp;&nbsp;<span class='total-entries'>Total Entries: " . $total_entries . "</span><hr>";
    echo "</form>";


    echo "<div class='table-responsive'>"; // Wrapper for responsive table
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<thead><tr>"; // Added thead for semantic HTML
    echo "<th><input type='checkbox' id='selectAllCheckbox' onchange='selectAll(this)'></th>";
    echo "<th onclick='sortTable(0)'>TUID</th>";
    echo "<th onclick='sortTable(9)'>Book ID</th>";
    echo "<th onclick='sortTable(8)'>Barcode</th>";
    echo "<th onclick='sortTable(1)'>Course</th>";
    echo "<th onclick='sortTable(2)'>Course Title</th>";
    echo "<th onclick='sortTable(3)'>Book Title</th>";
    echo "<th onclick='sortTable(4)'>Prof Name</th>";
    echo "<th onclick='sortTable(5)'>C/O</th>";
    echo "<th onclick='sortTable(6)'>Last C/O</th>";
    echo "<th onclick='sortTable(7)'>Expected Return</th>";
    echo "<th>Actions</th>";
    echo "</tr></thead><tbody>"; // Added tbody
    while($row = $result->fetch_assoc()) {
        $class = $row["CheckedOut"] == "yes" ? "checked-out" : "";
        echo "<tr class='".$class."'>";
        echo "<td><input type='checkbox' name='checked' value='1' data-barcode='".$row["Barcode"]."' onchange='toggleSelected(this)'";
        // Check if barcode is selected and mark checkbox accordingly
        $sql_selected = "SELECT COUNT(*) AS count FROM selected WHERE barcode='".$row["Barcode"]."'";
        $result_selected = $conn->query($sql_selected);
        $row_selected = $result_selected->fetch_assoc();
        if ($row_selected["count"] > 0) {
            echo " checked";
        }
        echo "></td>";
        // Added data-label for mobile responsiveness
        echo "<td data-label='TUID:'><span class='popup' onmouseover='loadPopupContent(\"".$row["TUID"]."\", this.querySelector(\".popuptext\"))'>".$row["TUID"]."<span class='popuptext'><div></div></span></span></td>";
        echo "<td data-label='Book ID:'>".$row["Book"]."</td>";
        echo "<td data-label='Barcode:'>".$row["Barcode"]."</td>";
        echo "<td data-label='Course:'>".$row["Course"]."</td>";
        echo "<td data-label='Course Title:'>".$row["course title"]."</td>";
        echo "<td data-label='Book Title:'>".$row["book title"]."</td>";
        echo "<td data-label='Prof Name:'>".$row["Name"]."</td>";
        echo "<td data-label='C/O:'>".$row["CheckedOut"]."</td>";
        echo "<td data-label='Last C/O:'>".$row["Last Checkout"]."</td>";
        echo "<td data-label='Expected Return:'>".$row["Expected Return"]."</td>";
        
        // If checkedout is 'yes', display a return button
        if($row["CheckedOut"] == "yes") {
            echo "<td><form method='post' onsubmit='return confirmReturn()'><input type='hidden' name='return_barcode' value='".$row["Barcode"]."'><input type='submit' value='Return' class='btn-secondary btn-small'></form></td>";
        } else {
            echo "<td></td>"; // Empty cell if not checked out
        }
        
        echo "</tr>";
    }
    echo "</tbody></table>"; // Close tbody and table
    echo "</div>"; // Close table-responsive wrapper
} else {
    echo "No textbooks added.";
}
$conn->close();
?>

<script>
// Function to fetch content from PHP file and display in popup
function loadPopupContent(tuid, popupDiv) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            popupDiv.innerHTML = this.responseText;
            popupDiv.querySelector('div').style.display = 'block';
        }
    };
    xhttp.open("GET", "activearchive.php?tuid=" + tuid, true);
    xhttp.send();
}

// Function to sort table rows based on column index
function sortTable(columnIndex) {
    var table, rows, switching, i, x, y, shouldSwitch;
    table = document.querySelector('table');
    switching = true;
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("td")[columnIndex];
            y = rows[i + 1].getElementsByTagName("td")[columnIndex];
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
}

// Event listeners to trigger sorting when table headers are clicked
document.addEventListener('DOMContentLoaded', function() {
    var headers = document.querySelectorAll('th');
    headers.forEach(function(header, index) {
        header.addEventListener('click', function() {
            sortTable(index);
        });
    });
});

// Load checkbox states from local storage and update checkboxes
document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function(checkbox) {
        var barcode = checkbox.getAttribute('data-barcode');
        var isChecked = localStorage.getItem(barcode) === 'true';
        checkbox.checked = isChecked;
        // Update "selected" list on page load
        if (isChecked) {
            addToSelected(barcode);
        }
    });
});

// Store checkbox state in local storage and update "selected" list
function toggleSelected(checkbox) {
    var barcode = checkbox.getAttribute('data-barcode');
    var isChecked = checkbox.checked;
    localStorage.setItem(barcode, isChecked);
    if (isChecked) {
        addToSelected(barcode);
    } else {
        removeFromSelected(barcode);
    }
}

// Add barcode to "selected" list
function addToSelected(barcode) {
    var formData = new FormData();
    formData.append('selected_barcode', barcode);
    formData.append('checked', 1);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    }).then(data => {
        console.log(data);
    }).catch(error => {
        console.error('There was an error!', error);
    });
}

// Remove barcode from "selected" list
function removeFromSelected(barcode) {
    var formData = new FormData();
    formData.append('selected_barcode', barcode);
    formData.append('checked', 0); // Send 0 to indicate unchecked

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    }).then(data => {
        console.log(data);
    }).catch(error => {
        console.error('There was an error!', error);
    });
}

// Function to select all checkboxes
function selectAll(checkbox) {
    var checkboxes = document.querySelectorAll('input[name="checked"]');
    checkboxes.forEach(function(cb) { // Changed 'checkbox' to 'cb' to avoid conflict
        cb.checked = !cb.checked; // Toggle the checked state
        var barcode = cb.getAttribute('data-barcode');
        toggleSelected(cb); // Toggle selected state
    });
}

// Function to confirm book return
function confirmReturn() {
    return confirm("Are you sure you want to return this book ? (The TUID will be deleted from the database)");
}
</script>

</body>
</html>
