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

// --- Check if the request method is POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// --- Get the array of IDs from the POST request ---
$ids = $_POST['ids'] ?? null;

// --- Validate input ---
if (empty($ids) || !is_array($ids)) {
    $response['message'] = 'Error: No item IDs provided.';
    echo json_encode($response);
    exit;
}

// --- Sanitize all IDs to ensure they are integers ---
$sanitized_ids = [];
foreach ($ids as $id) {
    if (ctype_digit((string)$id)) {
        $sanitized_ids[] = (int)$id;
    } else {
        // Stop if any ID is invalid
        $response['message'] = 'Error: Invalid ID detected.';
        echo json_encode($response);
        exit;
    }
}

if (empty($sanitized_ids)) {
    $response['message'] = 'Error: No valid item IDs to delete.';
    echo json_encode($response);
    exit;
}

// --- Prepare the SQL DELETE statement ---
// We create a string of placeholders (?, ?, ?) based on the count of IDs
$placeholders = implode(',', array_fill(0, count($sanitized_ids), '?'));
$types = str_repeat('i', count($sanitized_ids)); // 'i' for integer

$sql = "DELETE FROM textbooks WHERE id IN ($placeholders)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind all integer IDs
    $stmt->bind_param($types, ...$sanitized_ids);
    
    // Execute the statement
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        if ($affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Successfully deleted $affected_rows item(s).";
        } else {
            $response['message'] = 'No items were deleted. They may have already been removed.';
        }
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
