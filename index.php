<?php
// --- Include the database connection file ---
require_once 'db_connect.php'; 

// --- SQL Query to fetch data ---
// Using SELECT * is fine, but for production, it's better to list columns
// e.g., SELECT id, tuid, course, course_title, ... FROM textbooks
$sql = "SELECT * FROM textbooks";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Textbooks List</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <h2>All Textbooks from LTS2 Database</h2>

    <table>
        <thead>
            <tr>
                <th>TUID</th>
                <th>Course</th>
                <th>Course Title</th>
                <th>Book Title</th>
                <th>Name</th>
                <th>Checked Out</th>
                <th>Last Checkout</th>
                <th>Expected Return</th>
                <th>Barcode</th>
                <th>Book</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Loop through each row of the result
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    // Manually specify which column goes into which cell
                    // The key in $row['key_name'] MUST match your database column name
                    echo "<td>" . htmlspecialchars($row['tuid'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['course'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['course title'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['book title'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['checkedout'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['last checkout'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['expected return'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['barcode'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['book'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['id'] ?? '') . "</td>";
                    echo "</tr>";
                }
            } else {
                // Display a "no results" message spanning all 11 columns
                echo "<tr><td colspan='11'>0 results found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
// --- Close Connection ---
$conn->close();
?>
