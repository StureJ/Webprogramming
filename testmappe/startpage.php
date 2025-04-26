<?php
session_start();


//Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movdle"; //Navnet på den overordnet database


//Connecter til MySQL **Husk at brug XAMPP**
$conn = new mysqli($servername, $username, $password);


//Check om den connecter
if ($conn->connect_error) {
    die("Fejl" . $conn->connect_error);
}


//Hvis der ikke er lavet en database, laver vi en med navnet Modvle og vælger den.
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql); //Lav databasen

$conn->select_db($dbname);
//Table "Users" hvis der ikke er en, skal virke local.
$tableSql = "CREATE TABLE IF NOT EXISTS Users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(30) NOT NULL,
    Succesful_attempt INT(2) NOT NULL,
    Movie_name VARCHAR(30) NOT NULL
)";
$conn->query($tableSql); //Lav tablen

//Closer connectionen når den er færdig.
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Movdle - the game!</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
    <h1>Movdle</h1>
    <form action="play_game.php" method="POST">
        Enter your username: <input type="text" name="username" required>
        <input type="submit" value="Start Game">
    </form>
</body>
</html>