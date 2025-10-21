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

// --- Get form data from POST request ---
$barcode = $_POST['barcode'] ?? null;
$bookTitle = $_POST['book_title'] ?? null;
$course = $_POST['course'] ?? null;
$courseTitle = $_POST['course_title'] ?? null;
$name = $_POST['name'] ?? null;
$book = $_POST['book'] ?? null;

// --- Basic Validation ---
if (empty($barcode) || empty($bookTitle)) {
    $response['message'] = 'Error: Barcode and Book Title are required fields.';
    echo json_encode($response);
    exit;
}

// --- Prepare the SQL INSERT statement to prevent SQL injection ---
$sql = "INSERT INTO textbooks (barcode, `book title`, course, `course title`, name, book) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters - all are strings ('s')
    // Set empty strings to NULL if you prefer
    $param_course = !empty($course) ? $course : null;
    $param_courseTitle = !empty($courseTitle) ? $courseTitle : null;
    $param_name = !empty($name) ? $name : null;
    $param_book = !empty($book) ? $book : null;

    $stmt->bind_param(
        'ssssss',
        $barcode,
        $bookTitle,
        $param_course,
        $param_courseTitle,
        $param_name,
        $param_book
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'New item added successfully!';
        } else {
            $response['message'] = 'Failed to add item. No rows were affected.';
        }
    } else {
        // Check for duplicate entry error (code 1062)
        if ($conn->errno == 1062) {
             $response['message'] = 'Error: An item with this barcode or ID already exists.';
        } else {
             $response['message'] = 'Database execution failed: ' . $stmt->error;
        }
    }
    $stmt->close();
} else {
    $response['message'] = 'Database statement preparation failed: ' . $conn->error;
}

// --- Close the connection and send the response ---
$conn->close();
echo json_encode($response);
?>
