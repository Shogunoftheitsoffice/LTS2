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
$tuid = $_POST['tuid'] ?? null;

// --- Validate input ---
if (empty($bookId) || empty($tuid)) {
    $response['message'] = 'Error: Missing Book ID or TUID.';
    echo json_encode($response);
    exit;
}

if (!preg_match('/^\d{9}$/', $tuid)) {
    $response['message'] = 'Error: TUID must be exactly 9 digits.';
    echo json_encode($response);
    exit;
}

// --- Prepare and execute the database update ---
// We set checkedout, tuid, and the last checkout date.
// We also add a condition to ensure we're only checking out an available book.
$sql = "UPDATE textbooks 
        SET 
            `checkedout` = 'Yes', 
            `tuid` = ?, 
            `last checkout` = NOW() 
        WHERE 
            `id` = ? AND 
            (`checkedout` IS NULL OR `checkedout` = 'No' OR `checkedout` = '')";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 's' for tuid (safer as a string), 'i' for id (integer)
    $stmt->bind_param('si', $tuid, $bookId);
    
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
