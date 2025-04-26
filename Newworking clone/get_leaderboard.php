<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch leaderboard data
$sql = "SELECT Username, Succesful_attempt FROM Users ORDER BY Succesful_attempt DESC"; // Descending order for leaderboard
$result = $conn->query($sql);

$leaderboard = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = $row;
    }
}

echo json_encode($leaderboard);

$conn->close();
?>
