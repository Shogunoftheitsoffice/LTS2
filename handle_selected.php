<?php
$servername = "localhost";
$username = "admins";
$password = "fcabfo505#";
$dbname = "LTS";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $barcode = mysqli_real_escape_string($conn, $_POST["barcode"]);

    // Check if the entry exists in the main table
    $sqlCheckEntry = "SELECT 1 FROM textbooks WHERE barcode = '$barcode'";
    $resultCheckEntry = $conn->query($sqlCheckEntry);

    if ($resultCheckEntry->num_rows === 0) {
        // If entry doesn't exist in the main table, remove it from the 'selected' table
        $sqlDeleteSelected = "DELETE FROM selected WHERE barcode = '$barcode'";
        if ($conn->query($sqlDeleteSelected) === TRUE) {
            echo "Barcode removed from 'selected' table as it doesn't exist in the main table!";
        } else {
            echo "Error: " . $sqlDeleteSelected . "<br>" . $conn->error;
        }
    } else {
        // Entry exists in the main table, perform requested action (add/remove)
        if ($action === "add") {
            $sqlInsert = "INSERT INTO selected (barcode) VALUES ('$barcode')";
            if ($conn->query($sqlInsert) === TRUE) {
                echo "Barcode inserted into 'selected' table successfully!";
            } else {
                echo "Error: " . $sqlInsert . "<br>" . $conn->error;
            }
        } elseif ($action === "remove") {
            $sqlDelete = "DELETE FROM selected WHERE barcode = '$barcode'";
            if ($conn->query($sqlDelete) === TRUE) {
                echo "Barcode removed from 'selected' table successfully!";
            } else {
                echo "Error: " . $sqlDelete . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>
