<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';

// --- Include the PhpSpreadsheet library ---
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// --- Fetch data from MySQL ---
$sql = "SELECT barcode, `book title`, course, `course title`, name, book, `last checkout`, tuid, `expected return` 
        FROM textbooks";
$result = $conn->query($sql);

// Create a new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add headers - I've added the other checkout fields too
$sheet->setCellValue('A1', 'Barcode');
$sheet->setCellValue('B1', 'Book Title');
$sheet->setCellValue('C1', 'Course');
$sheet->setCellValue('D1', 'Course Title');
$sheet->setCellValue('E1', 'Name');
$sheet->setCellValue('F1', 'Book');
$sheet->setCellValue('G1', 'Last Checkout');
$sheet->setCellValue('H1', 'TUID');
$sheet->setCellValue('I1', 'Expected Return');

// Add data from MySQL
if ($result && $result->num_rows > 0) {
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowIndex, $row['barcode']);
        $sheet->setCellValue('B' . $rowIndex, $row['book title']);
        $sheet->setCellValue('C' . $rowIndex, $row['course']);
        $sheet->setCellValue('D' . $rowIndex, $row['course title']);
        $sheet->setCellValue('E' . $rowIndex, $row['name']);
        $sheet->setCellValue('F' . $rowIndex, $row['book']);
        $sheet->setCellValue('G' . $rowIndex, $row['last checkout']);
        $sheet->setCellValue('H' . $rowIndex, $row['tuid']);
        $sheet->setCellValue('I' . $rowIndex, $row['expected return']);
        $rowIndex++;
    }
}

// Close connection
$conn->close();

// Create a writer
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

// Set headers for Excel file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="LTS_Textbook_Export.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to output
$writer->save('php://output');

exit(); // Exit the script after downloading the file
?>
