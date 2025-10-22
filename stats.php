<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Include the database connection ---
require_once 'db_connect.php';

$response = [
    'success' => false,
    'stats' => null,
    'message' => 'An error occurred.'
];

try {
    $stats = [];

    // 1. Total number of books
    $result = $conn->query("SELECT COUNT(*) as total FROM textbooks");
    $stats['total_books'] = $result->fetch_assoc()['total'];

    // 2. Number of books checked out
    $result = $conn->query("SELECT COUNT(*) as total FROM textbooks WHERE checkedout = 'Yes'");
    $stats['checked_out'] = $result->fetch_assoc()['total'];

    // 3. Number of books available (calculated)
    $stats['available'] = $stats['total_books'] - $stats['checked_out'];

    // 4. Number of books currently overdue
    $result = $conn->query("SELECT COUNT(*) as total FROM textbooks WHERE checkedout = 'Yes' AND `expected return` < NOW()");
    $stats['overdue'] = $result->fetch_assoc()['total'];

    // 5. Total all-time checkouts (sum of TimesCO)
    $result = $conn->query("SELECT SUM(TimesCO) as total FROM textbooks");
    // Use ?? 0 to handle case where no books have ever been checked out
    $stats['total_checkouts'] = $result->fetch_assoc()['total'] ?? 0;

    // 6. Most checked-out book
    $result = $conn->query("SELECT `book title`, `TimesCO` FROM textbooks WHERE `TimesCO` > 0 ORDER BY `TimesCO` DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $pop = $result->fetch_assoc();
        $stats['most_popular'] = [
            'title' => $pop['book title'],
            'count' => $pop['TimesCO']
        ];
    } else {
        // Default if no book has checkouts
        $stats['most_popular'] = [
            'title' => 'N/A',
            'count' => 0
        ];
    }

    $response['success'] = true;
    $response['stats'] = $stats;

} catch (Exception $e) {
    $response['message'] = 'Database query failed: ' . $e->getMessage();
}

// --- Close the connection and send the response ---
$conn->close();
echo json_encode($response);
?>
