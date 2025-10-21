<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Include the database connection and autoloader ---
require_once 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// --- Initialize response array ---
$response = [
    'success' => false,
    'message' => 'An error occurred.',
    'processed' => 0,
    'inserted' => 0,
    'updated' => 0
];

// --- Check if a file has been uploaded ---
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'No file uploaded or an upload error occurred.';
    echo json_encode($response);
    exit;
}

$file = $_FILES['file']['tmp_name'];

try {
    // Load the spreadsheet file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    // --- PREPARE STATEMENTS (FOR SECURITY & PERFORMANCE) ---
    
    // 1. Check if barcode exists
    $checkSql = "SELECT id FROM textbooks WHERE barcode = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);

    // 2. Update existing row
    $updateSql = "UPDATE textbooks SET 
                    `book title` = ?, 
                    course = ?, 
                    `course title` = ?, 
                    name = ?, 
                    book = ?
                  WHERE barcode = ?";
    $updateStmt = $conn->prepare($updateSql);

    // 3. Insert new row
    $insertSql = "INSERT INTO textbooks (barcode, `book title`, course, `course title`, name, book) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);

    if (!$checkStmt || !$updateStmt || !$insertStmt) {
        $response['message'] = 'Database statement preparation failed: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    // --- Loop through rows in the spreadsheet ---
    foreach ($sheet->getRowIterator() as $row) {
        if ($row->getRowIndex() === 1) {
            continue; // Skip the header row
        }
        $response['processed']++;

        $rowData = [];
        // Get all cell values for the current row
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Iterate all cells, even if empty
        foreach ($cellIterator as $cell) {
            $rowData[] = $cell->getValue();
        }

        // --- Assign data from spreadsheet ---
        // We use htmlspecialchars as a basic sanitization for string data
        $barcode = trim($rowData[0] ?? null);
        $bookTitle = trim($rowData[1] ?? null);
        $course = trim($rowData[2] ?? null);
        $courseTitle = trim($rowData[3] ?? null);
        $name = trim($rowData[4] ?? null);
        $book = trim($rowData[5] ?? null);
        
        // Skip row if barcode or title is missing
        if (empty($barcode) || empty($bookTitle)) {
            continue;
        }

        // --- Execute "Upsert" Logic ---
        // 1. Check if barcode exists
        $checkStmt->bind_param('s', $barcode);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            // 2. Barcode exists -> UPDATE
            $updateStmt->bind_param('ssssss', $bookTitle, $course, $courseTitle, $name, $book, $barcode);
            $updateStmt->execute();
            $response['updated']++;
        } else {
            // 3. Barcode does not exist -> INSERT
            $insertStmt->bind_param('ssssss', $barcode, $bookTitle, $course, $courseTitle, $name, $book);
            $insertStmt->execute();
            $response['inserted']++;
        }
    }

    // Close statements
    $checkStmt->close();
    $updateStmt->close();
    $insertStmt->close();
    
    $response['success'] = true;
    $response['message'] = "Import successful! Processed: {$response['processed']}, Inserted: {$response['inserted']}, Updated: {$response['updated']}.";

} catch (Exception $e) {
    // Catch errors from PhpSpreadsheet
    $response['message'] = 'Error reading spreadsheet file: ' . $e->getMessage();
}

// Close connection
$conn->close();

// --- Echo the final JSON response ---
echo json_encode($response);
?>
