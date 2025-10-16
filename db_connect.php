<?php
// --- Central Database Connection File ---

$servername = "localhost"; // Or your server IP
$username = "root";        // Your database username
$password = "Beagle02!";            // Your database password
$dbname = "LTS2";          // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Stop the script and show a generic error to the user
    die("Error: Could not connect to the database."); 
}
?>
