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

// --- Validate input ---
if (empty($bookId)) {
    $response['message'] = 'Error: Missing Book ID.';
    echo json_encode($response);
    exit;
}

// --- Prepare and execute the database update ---
// We reset all checkout-related fields to NULL or 'No'
$sql = "UPDATE textbooks 
        SET 
            `checkedout` = 'No', 
            `tuid` = NULL, 
            `name` = NULL,
            `last checkout` = NULL,
            `expected return` = NULL
        WHERE 
            `id` = ? AND 
            `checkedout` = 'Yes'";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 'i' for id (integer)
    $stmt->bind_param('i', $bookId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Book returned successfully!';
        } else {
            $response['message'] = 'Book was not checked out or could not be found.';
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
