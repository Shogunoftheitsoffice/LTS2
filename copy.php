<!DOCTYPE html>
<html>
<head>
    <title>Copy</title>
</head>
<body>

<?php
$servername = "localhost";
$username = "tyler";
$password = "Beagle02!";
$dbname = "LTS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if(isset($_POST['copy_all'])){
    // Check if any data is submitted
    if (!empty($_POST['barcode'])) {
        // Loop through each submitted entry
        foreach ($_POST['barcode'] as $key => $barcode) {
            // Get corresponding values for each field
            $bookTitle = $_POST['book_title'][$key];
            $course = $_POST['course'][$key];
            $courseTitle = $_POST['course_title'][$key];
            $name = $_POST['name'][$key];
            $book = $_POST['book'][$key];
            
            // Insert data into textbooks table
            $insertSql = "INSERT INTO textbooks (barcode, `book title`, course, `course title`, name, book) 
                          VALUES ('$barcode', '$bookTitle', '$course', '$courseTitle', '$name', '$book')";
            $conn->query($insertSql);
        }
        
        // Remove all entries from selected table
        $truncateSql = "TRUNCATE TABLE selected";
        $conn->query($truncateSql);
        
        echo "All entries copied successfully and removed from 'selected' table.";
        
        // Redirect to index.php
        header("Location: index.php");
        exit(); // Ensure no further output is sent
    } else {
        echo "No entries found in the form.";
    }
}

// SQL query to join "selected" and "textbooks" tables based on matching barcode
$sql = "SELECT s.barcode, t.`book title` AS book_title, t.course, t.`course title`, t.name, t.book
        FROM selected s 
        INNER JOIN textbooks t ON s.barcode = t.barcode";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output form and table header
    echo "<form method='POST'>";
    echo "<table border='1'><tr><th>Barcode</th><th>Book Title</th><th>Course</th><th>Course Title</th><th>Name</th><th>Book</th></tr>";

    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><input type='text' name='barcode[]' value='" . $row["barcode"]. "'></td>";
        echo "<td><input type='text' name='book_title[]' value='" . $row["book_title"]. "'></td>";
        echo "<td><input type='text' name='course[]' value='" . $row["course"]. "'></td>";
        echo "<td><input type='text' name='course_title[]' value='" . $row["course title"]. "'></td>"; // Adjusted here
        echo "<td><input type='text' name='name[]' value='" . $row["name"]. "'></td>";
        echo "<td><input type='text' name='book[]' value='" . $row["book"]. "'></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='submit' name='copy_all' value='Copy All'>";
    echo "</form>";
} else {
    echo "No textbooks selected!";
}
$conn->close();
?>
