<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Username, Succesful_attempt FROM Users ORDER BY Succesful_attempt ASC";
$result = $conn->query($sql);

echo "<h2>Leaderboard</h2>";
echo "<table border='1'>
<tr><th>Username</th><th>Successful Attempts</th></tr>";

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>".$row["Username"]."</td><td>".$row["Succesful_attempt"]."</td></tr>";
  }
} else {
  echo "No results.";
}

echo "</table>";

$conn->close();
?>
