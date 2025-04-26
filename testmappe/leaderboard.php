<?php
session_start(); //Session skal starte så kan vi få navnet på poster

//Database 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle"; 

//Connecter til MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

//Check om den connecter
if ($conn->connect_error) {
   die("Fejl leaderboard" . $conn->connect_error);
}

//Vi får navnet på filmen der er blevet valgt
$movie_name = $_SESSION['target_word'] ?? ''; 

//Query henter leaderboard data for den valgte film.
$sql = "SELECT Username, Succesful_attempt FROM Users WHERE movie_name = '$movie_name' ORDER BY Succesful_attempt ASC"; 
$result = $conn->query($sql);

$leaderboard = array();
if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
       $leaderboard[] = $row;
   }
}

echo json_encode($leaderboard); //Laver om til JSON

//Luk
$conn->close();
?>