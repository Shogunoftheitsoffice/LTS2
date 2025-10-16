<?php
// --- Include the database connection file ---
// This line brings in the $conn variable from db_connect.php
require_once 'db_connect.php'; 

// --- SQL Query to fetch data ---
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
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <h2>All Textbooks from LTS2 Database</h2>

    <table>
        <thead>
            <tr>
                <?php
                if ($result->num_rows > 0) {
                    $fields = $result->fetch_fields();
                    foreach ($fields as $field) {
                        echo "<th>" . htmlspecialchars($field->name) . "</th>";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $result->data_seek(0); 
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
            } else {
                $column_count = $conn->query("SELECT * FROM textbooks LIMIT 0")->field_count;
                echo "<tr><td colspan='" . $column_count . "'>0 results found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
// --- Close Connection ---
// The $conn variable is available here because we included the other file
$conn->close();
?>
