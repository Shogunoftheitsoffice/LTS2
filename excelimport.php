<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if a file has been uploaded
if(isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];

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

    // Load the spreadsheet file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    // Prepare a placeholder for the barcode values to be inserted
    $barcodeValues = [];

    // Loop through rows in the spreadsheet starting from the second row
    foreach ($sheet->getRowIterator() as $row) {
        if ($row->getRowIndex() === 1) {
            continue; // Skip the header row
        }

        $rowData = [];
        foreach ($row->getCellIterator() as $cell) {
            $rowData[] = $cell->getValue();
        }

        // Assuming the order of data in the spreadsheet matches the table structure in the database
        $barcode = $conn->real_escape_string($rowData[0]);
        $bookTitle = $conn->real_escape_string($rowData[1]);
        $course = $conn->real_escape_string($rowData[2]);
        $courseTitle = $conn->real_escape_string($rowData[3]);
        $name = $conn->real_escape_string($rowData[4]);
        $book = $conn->real_escape_string($rowData[5]);
        $lastCheckout = isset($rowData[6]) ? $conn->real_escape_string($rowData[6]) : null; // New column "last checkout"

        // Store the barcode values to be used in the SQL query
        $barcodeValues[] = "'$barcode'";

        // Check if the barcode already exists in the database
        $checkSql = "SELECT COUNT(*) as count FROM textbooks WHERE barcode = '$barcode'";
        $checkResult = $conn->query($checkSql);
        $rowCount = $checkResult->fetch_assoc()['count'];

        // If the barcode already exists, update the corresponding row
        if ($rowCount > 0) {
            $updateSql = "UPDATE textbooks SET `book title` = '$bookTitle', 
                                                course = '$course', 
                                                `course title` = '$courseTitle', 
                                                name = '$name', 
                                                book = '$book',
                                                `last checkout` = " . ($lastCheckout ? "'$lastCheckout'" : "NULL") . "
                          WHERE barcode = '$barcode'";
            $conn->query($updateSql);
        } else {
            // If the barcode does not exist, insert a new row
            $insertSql = "INSERT INTO textbooks (barcode, `book title`, course, `course title`, name, book, `last checkout`) 
                          VALUES ('$barcode', '$bookTitle', '$course', '$courseTitle', '$name', '$book', " . ($lastCheckout ? "'$lastCheckout'" : "NULL") . ")";
            $conn->query($insertSql);
        }
    }

    // Close connection
    $conn->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Import</title>
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
</head>
<body>
    <div class="container">
        <h2>Import to my database</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit" name="submit" class="btn-success">Import</button>
        </form>
        <br>
        <a href="index.php"><button class="btn-secondary">Back</button></a>
    </div>
</body>
</html>
