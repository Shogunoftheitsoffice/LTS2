<?php
// --- Set the response type to JSON ---
header('Content-Type: application/json');

// --- Include the database connection ---
require_once 'db_connect.php';

// --- Get the search term from the request ---
$searchTerm = $_GET['term'] ?? '';
$results = [];

// --- Proceed only if the search term is not empty ---
if (!empty($searchTerm)) {
    // --- Create a search pattern for the SQL LIKE clause ---
    $searchQuery = "%" . $searchTerm . "%";

    // --- Prepare the SQL statement to prevent SQL injection ---
    // This query searches across six common fields for a match.
    $sql = "SELECT * FROM textbooks WHERE 
            `book title` LIKE ? OR 
            `tuid` LIKE ? OR 
            `course` LIKE ? OR 
            `barcode` LIKE ? OR 
            `name` LIKE ? OR
            `course title` LIKE ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // --- Bind the search term to each placeholder in the query ---
        // 's' denotes the type is a string. We have 6 placeholders.
        $stmt->bind_param('ssssss', $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
        
        // --- Execute the query and get the results ---
        $stmt->execute();
        $result = $stmt->get_result();

        // --- Fetch each result row and add it to our results array ---
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
        }
        $stmt->close();
    }
}

// --- Close the database connection ---
$conn->close();

// --- Echo the results as a JSON-formatted string ---
echo json_encode($results);
?>
