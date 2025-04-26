<?php
session_start(); // Make sure session is started to access current movie name

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current movie name from session
$movie_name = $_SESSION['target_word'] ?? ''; // Ensure this is set properly in the session

// Query to fetch leaderboard data for the current movie
$sql = "SELECT Username, Succesful_attempt FROM Users WHERE movie_name = '$movie_name' ORDER BY Succesful_attempt ASC"; // Ascending order for leaderboard
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
