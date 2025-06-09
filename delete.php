<?php

$servername = "localhost";
$username = "tyler";
$password = "Beagle02!";
$dbname = "LTS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select barcodes from the 'selected' table
$sql_selected = "SELECT barcode FROM selected";
$result_selected = $conn->query($sql_selected);

if ($result_selected->num_rows > 0) {
    // Loop through each barcode
    while($row_selected = $result_selected->fetch_assoc()) {
        $barcode = $row_selected["barcode"];

        // Delete entries in the 'textbooks' table associated with the barcode
        $sql_delete_textbooks = "DELETE FROM textbooks WHERE barcode = '$barcode'";
        if ($conn->query($sql_delete_textbooks) === TRUE) {
            echo "Entry associated with barcode $barcode deleted successfully!.<br>";

            // Delete entry from the 'selected' table
            $sql_delete_selected = "DELETE FROM selected WHERE barcode = '$barcode'";
            if ($conn->query($sql_delete_selected) === TRUE) {
                echo "<br>";
            } else {
                echo "Error deleting entry from 'selected': " . $conn->error . "<br>";
            }
        } else {
            echo "Error deleting entries from 'textbooks': " . $conn->error . "<br>";
        }
    }
} else {
    echo "No textbooks selected!<br>";
}

// Close connection
$conn->close();
?>
