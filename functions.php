<?php

//Vælger et tidfældigt billede fra mappen Movie_Posters (Husk mappen skal være i htdocs)
function getRandomMoviePoster($folder = 'Movie_posters') 
{
    $images = glob($folder . '/*.{jpg}', GLOB_BRACE);
    if (count($images) > 0) 
    {
        return $images[array_rand($images)];
    }
    return '';
}

//Function til at sammenligne user's guess med svaret
function compareGuess($guess, $target) 
{
    $result = [];
    $targetLetters = str_split($target);
    $guessLetters = str_split($guess);
    $targetLetterCount = array_count_values($targetLetters);

    //Første tjek for rigtige bogstaver (exact match)
    for ($i = 0; $i < strlen($guess); $i++) 
    {
        if ($guessLetters[$i] === $targetLetters[$i]) 
        {
            $result[$i] = 'correct';  // Rigtige bogstaver i rigtig rækkefælde
            $targetLetterCount[$guessLetters[$i]]--;  // Undgå at bruge samme bogstav flere gange
        } else {
            $result[$i] = ''; // Placeholder for nu
        }
    }

    //Andet tjek: rigtige bogstaver, forkert placering
    for ($i = 0; $i < strlen($guess); $i++) 
    {
        if ($result[$i] === '') 
        {
            if (in_array($guessLetters[$i], $targetLetters) && $targetLetterCount[$guessLetters[$i]] > 0) 
            {
                $result[$i] = 'misplaced'; //Misplaced (Gult)
                $targetLetterCount[$guessLetters[$i]]--; // Samme som før
            } else 
            {
                $result[$i] = 'incorrect'; //Forkert alting
            }
        }
    }

    return $result;
}

//Function for at uploade win
function saveWinToDatabase($username, $attempts, $movie_name) 
{
    $servername = "localhost";
    $dbUsername = "root"; //Det er root fordi XAMPP
    $password = "";
    $dbname = "Movdle";

    $conn = new mysqli($servername, $dbUsername, $password, $dbname);
    if ($conn->connect_error) 
    {
        die("Connection Fejl saveWinToDatabase: " . $conn->connect_error);
    }

    //Tjek om user har gættet
    $checkUserSql = "SELECT * FROM Users WHERE Username = '$username' AND movie_name = '$movie_name'";
    $result = $conn->query($checkUserSql);

    if ($result->num_rows > 0) //Hvis ja
    {
        $row = $result->fetch_assoc(); //Find rækken
        if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) //Fejl eller bedre gæt, opdater
        {
            $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$username' AND movie_name = '$movie_name'";
            $conn->query($updateSql); //Opdater
        }
    } else //Hvis de ikke har, lav en ny række
    {
        $sql = "INSERT INTO Users (Username, Succesful_attempt, movie_name) VALUES ('$username', '$attempts', '$movie_name')";
        $conn->query($sql);
    }

    $conn->close(); //Close connection til databasen
    return true; 
}
?>