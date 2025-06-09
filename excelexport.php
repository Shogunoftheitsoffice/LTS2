<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

// Fetch data from MySQL
$sql = "SELECT * FROM textbooks";
$result = $conn->query($sql);

// Create a new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add headers
$sheet->setCellValue('A1', 'Barcode');
$sheet->setCellValue('B1', 'Book Title');
$sheet->setCellValue('C1', 'Course');
$sheet->setCellValue('D1', 'Course Title');
$sheet->setCellValue('E1', 'Name');
$sheet->setCellValue('F1', 'Book');
$sheet->setCellValue('G1', 'Last Checkout');

// Add data from MySQL
if ($result->num_rows > 0) {
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['barcode']);
        $sheet->setCellValue('B' . $rowIndex, $row['book title']);
        $sheet->setCellValue('C' . $rowIndex, $row['course']);
        $sheet->setCellValue('D' . $rowIndex, $row['course title']);
        $sheet->setCellValue('E' . $rowIndex, $row['name']);
        $sheet->setCellValue('F' . $rowIndex, $row['book']);
        $sheet->setCellValue('G' . $rowIndex, $row['last checkout']);
        $rowIndex++;
    }
}

// Create a writer
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

// Set headers for Excel file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="textbooks.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to output
$writer->save('php://output');

// Close connection
$conn->close();

exit(); // Exit the script after downloading the file

?>
