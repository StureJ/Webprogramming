<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['username'];
$success = $_GET['success']; // number of attempts, or 0 if failed

$sql = "INSERT INTO Users (Username, Succesful_attempt) VALUES ('$user', '$success')";

if ($conn->query($sql) === TRUE) {
  echo "Result saved successfully!";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

// Destroy session after game
session_destroy();
?>

<br>
<a href="leaderboard.php">See Leaderboard</a>
