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

// We require a specific confirmation text from the client to proceed
$confirmation = $_POST['confirmation'] ?? null;

if ($confirmation !== 'DELETE ALL') {
    $response['message'] = 'Confirmation text did not match. Deletion cancelled.';
    echo json_encode($response);
    exit;
}

// --- Prepare the SQL TRUNCATE statement ---
// TRUNCATE is faster than DELETE and resets auto-increment counters.
$sql = "TRUNCATE TABLE textbooks";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'All items have been permanently deleted.';
    } else {
        $response['message'] = 'Database execution failed: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['message'] = 'Database statement preparation failed: ' . $conn->error;
}

// --- Close the connection and send the response ---
$conn->close();
echo json_encode($response);
?>
