<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Include the database connection ---
require_once 'db_connect.php';

// --- Initialize response array ---
$response = [
    'success' => false,
    'message' => 'An error occurred.'
];

// --- Get data from the POST request ---
$bookId = $_POST['bookId'] ?? null;
$barcode = $_POST['barcode'] ?? null;
$tuid = $_POST['tuid'] ?? null;

// --- Validate input ---
if (empty($tuid)) {
    $response['message'] = 'Error: Missing TUID.';
    echo json_encode($response);
    exit;
}

if (empty($bookId) && empty($barcode)) {
    $response['message'] = 'Error: Missing Item ID or Barcode.';
    echo json_encode($response);
    exit;
}

if (!preg_match('/^\d{9}$/', $tuid)) {
    $response['message'] = 'Error: TUID must be exactly 9 digits.';
    echo json_encode($response);
    exit;
}

// --- Prepare and execute the database update ---
// We build the query based on whether we have an ID or a barcode
$whereField = '';
$paramType = '';
$paramValue = '';

if (!empty($bookId)) {
    $whereField = '`id` = ?';
    $paramType = 'i'; // integer
    $paramValue = $bookId;
} else {
    $whereField = '`barcode` = ?';
    $paramType = 's'; // string
    $paramValue = $barcode;
}

$sql = "UPDATE textbooks 
        SET 
            `checkedout` = 'Yes', 
            `tuid` = ?, 
            `last checkout` = NOW(),
            `expected return` = DATE_ADD(NOW(), INTERVAL 2 HOUR),
            `TimesCO` = IFNULL(`TimesCO`, 0) + 1
        WHERE 
            $whereField AND 
            (`checkedout` IS NULL OR `checkedout` = 'No' OR `checkedout` = '')";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind 's' for tuid, then either 'i' or 's' for the item identifier
    $stmt->bind_param('s' . $paramType, $tuid, $paramValue);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Book checked out successfully!';
        } else {
            // This happens if the book was already checked out (the WHERE clause failed)
            $response['message'] = 'Book is already checked out or could not be found.';
        }
    } else {
        $response['message'] = 'Database execution failed.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Database statement preparation failed.';
}

// --- Close the connection and send the response ---
$conn->close();
echo json_encode($response);
?>
