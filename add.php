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
if(isset($_POST['submit'])){
    // Get form data
    $barcode = $_POST['barcode'];
    $bookTitle = $_POST['book_title'];
    $course = $_POST['course'];
    $courseTitle = $_POST['course_title'];
    $name = $_POST['name'];
    $book = $_POST['book'];
    
    // Insert data into textbooks table
    $insertSql = "INSERT INTO textbooks (barcode, `book title`, course, `course title`, name, book) 
                  VALUES ('$barcode', '$bookTitle', '$course', '$courseTitle', '$name', '$book')";
    if ($conn->query($insertSql) === TRUE) {
        // Redirect to index.php after successful entry
        header("Location: index.php");
        exit(); // Ensure no further output is sent
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add</title>
</head>
<body>
    ‎<h2>New Textbook Entry</h2>
    <form method="POST">
        ‎<label for="barcode">Item Barcode:</label>
        ‎<input type="text" id="barcode" name="barcode" required><br><br>
<label for="book_title">Book Title:</label>
        ‎<input type="text" id="book_title" name="book_title" required><br><br>
<label for="course">Course:</label>
        ‎<input type="text" id="course" name="course" required><br><br>
‎<label for="course_title">Course Title:</label>
        ‎<input type="text" id="course_title" name="course_title" required><br><br>
‎<label for="name">Prof Name:</label>
        ‎<input type="text" id="name" name="name" required><br><br>
‎<label for="book">Book ID:</label>
        ‎<input type="text" id="book" name="book" required><br><br>
‎<input type="submit" name="submit" value="Submit">
    </form>
<br><br>
    <!-- Back button -->
    <form action="index.php">
        ‎<input type="submit" value="Go Back">
    </form>
</body>
</html>
