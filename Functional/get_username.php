<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle";  // Database name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
  echo "Database is ready or created successfully.\n";
} else {
  echo "Error creating database: " . $conn->error;
}

// Select the newly created or existing database
$conn->select_db($dbname);

// Create table if it doesn't exist
$tableSql = "CREATE TABLE IF NOT EXISTS Users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(30) NOT NULL,
    Succesful_attempt INT(2) NOT NULL
)";

if ($conn->query($tableSql) === TRUE) {
  echo "Table Users is ready.\n";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>

<html>
<body>

<form action="play_game.php" method="POST">
  Enter Username: <input type="text" name="username" required>
  <input type="submit" value="Start Game">
</form>

</body>
</html>
