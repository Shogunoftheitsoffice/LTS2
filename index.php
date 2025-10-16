<?php
// --- Include the database connection file ---
require_once 'db_connect.php';

// --- SQL Query to fetch data ---
$sql = "SELECT * FROM textbooks";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expandable Textbooks List</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        /* Style for the clickable book title row */
        .book-title-row td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            font-weight: bold;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        .book-title-row:hover td {
            background-color: #f1f1f1;
        }
        /* Add a "+" icon before the title */
        .book-title-row td::before {
            content: '+';
            display: inline-block;
            margin-right: 10px;
            font-size: 1.2em;
            color: #333;
        }
        /* Change the icon to "-" when the row is active/expanded */
        .book-title-row.active td::before {
            content: '−';
        }
        /* Hide the details row by default */
        .book-details-row {
            display: none;
        }
        .book-details-row td {
            border: 1px solid #ddd;
            border-top: none; /* Avoid double border */
            padding: 0;
        }
        /* Styling for the content inside the details row */
        .details-content {
            padding: 15px 20px;
            background-color: #ffffff;
            line-height: 1.6;
        }
        .details-content strong {
            display: inline-block;
            width: 150px; /* Aligns the values */
            color: #555;
        }
    </style>
</head>
<body>

    <h2>All Textbooks from LTS2 Database</h2>

    <table>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Loop through each row of the result
                while ($row = $result->fetch_assoc()) {
                    // We need a unique ID for each details row to target it with JavaScript
                    $details_id = "details-" . htmlspecialchars($row['id']);
            ?>

                    <tr class="book-title-row" data-target="#<?php echo $details_id; ?>">
                        <td><?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?></td>
                    </tr>

                    <tr class="book-details-row" id="<?php echo $details_id; ?>">
                        <td>
                            <div class="details-content">
                                <strong>TUID:</strong> <?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?><br>
                                <strong>Course:</strong> <?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?><br>
                                <strong>Course Title:</strong> <?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?><br>
                                <strong>Name:</strong> <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?><br>
                                <strong>Checked Out:</strong> <?php echo htmlspecialchars($row['checkedout'] ?? 'N/A'); ?><br>
                                <strong>Last Checkout:</strong> <?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?><br>
                                <strong>Expected Return:</strong> <?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?><br>
                                <strong>Barcode:</strong> <?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?><br>
                                <strong>Book:</strong> <?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?><br>
                                <strong>ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?>
                            </div>
                        </td>
                    </tr>

            <?php
                } // end while loop
            } else {
                // Display a "no results" message
                echo "<tr><td>0 results found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        // Wait for the document to be fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Get all the clickable title rows
            const titleRows = document.querySelectorAll('.book-title-row');

            // Add a click event listener to each one
            titleRows.forEach(row => {
                row.addEventListener('click', () => {
                    // Toggle the 'active' class on the clicked row for the +/- indicator
                    row.classList.toggle('active');

                    // Get the selector for the target details row from the 'data-target' attribute
                    const targetSelector = row.getAttribute('data-target');
                    const detailsRow = document.querySelector(targetSelector);

                    // Toggle the display of the details row
                    if (detailsRow) {
                        if (detailsRow.style.display === 'table-row') {
                            detailsRow.style.display = 'none';
                        } else {
                            detailsRow.style.display = 'table-row';
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>

<?php
// --- Close Connection ---
$conn->close();
?>
