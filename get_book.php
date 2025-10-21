<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Include the database connection ---
require_once 'db_connect.php';

// --- Initialize response array ---
$response = [
    'success' => false,
    'message' => 'An error occurred.',
    'book' => null
];

// --- Get book ID from the GET request ---
$bookId = $_GET['id'] ?? null;

// --- Validate input ---
if (empty($bookId) || !ctype_digit($bookId)) {
    $response['message'] = 'Invalid or missing Book ID.';
    echo json_encode($response);
    exit;
}

// --- Prepare and execute the database select ---
$sql = "SELECT * FROM textbooks WHERE id = ? LIMIT 1";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 'i' for id (integer)
    $stmt->bind_param('i', $bookId);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $response['success'] = true;
            $response['message'] = 'Book data retrieved.';
            $response['book'] = $result->fetch_assoc();
        } else {
            $response['message'] = 'Book not found in the database.';
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
