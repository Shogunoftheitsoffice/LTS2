<!DOCTYPE html>
<html>
<head>
  <title>Help/Feedback</title>
</head>
<body>
  <center><h2>Help page</h2></center>
  <hr>
  <center><p>If you need help find Tyler T</p></center>
  <center><a href="index.php"><button>Back</button></a></center>

  <center>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <label for="text">Send Feedback</label><br>
      <textarea id="text" name="text" rows="10" cols="50" oninput="updateCountdown()" maxlength="1000"></textarea><br>
      <p>Characters left: <span id="countdown">1000</span></p>
      <button type="submit" name="submit" id="submitBtn">Submit</button>
      <br><br><br><br>
    </form>
  </center>

<?php
$servername = "localhost";
$username = "tyler";
$password = "Beagle02!";
$dbname = "LTS";

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data into the 'feedback' table
    $text = $_POST['text'];
    $sql = "INSERT INTO feedback (soup) VALUES ('$text')";

    if ($conn->query($sql) === TRUE) {
        echo "Feedback sent!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
<script>
function updateCountdown() {
    var maxLength = 1000;
    var currentLength = document.getElementById("text").value.length;
    var countdown = document.getElementById("countdown");
    var submitBtn = document.getElementById("submitBtn");

    if (maxLength - currentLength >= 0) {
        countdown.innerText = maxLength - currentLength;
        submitBtn.disabled = false; // Enable the submit button
    } else {
        countdown.innerText = "Exceeded";
        submitBtn.disabled = true; // Disable the submit button
    }
}
</script>
</body>
</html>
