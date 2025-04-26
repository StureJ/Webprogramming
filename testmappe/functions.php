<?php
// Function to get a random movie poster from the folder
function getRandomMoviePoster($folder = 'Movie_posters') {
    $images = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (count($images) > 0) {
        return $images[array_rand($images)];
    }
    return '';
}

// Function to compare the guess with the target word
function compareGuess($guess, $target) {
    $result = [];
    $targetLetters = str_split($target);
    $guessLetters = str_split($guess);
    $targetLetterCount = array_count_values($targetLetters);

    // First pass: check for correct letters (exact match)
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($guessLetters[$i] === $targetLetters[$i]) {
            $result[$i] = 'correct';  // Correct letter in correct position
            $targetLetterCount[$guessLetters[$i]]--;  // Reduce count of that letter
        } else {
            $result[$i] = ''; // Placeholder for now
        }
    }

    // Second pass: check for misplaced letters
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($result[$i] === '') {
            if (in_array($guessLetters[$i], $targetLetters) && $targetLetterCount[$guessLetters[$i]] > 0) {
                $result[$i] = 'misplaced'; // Correct letter in the wrong position
                $targetLetterCount[$guessLetters[$i]]--; // Reduce count of that letter
            } else {
                $result[$i] = 'incorrect'; // Incorrect letter
            }
        }
    }

    return $result;
}

// Function to save win to database
function saveWinToDatabase($username, $attempts, $movie_name) {
    $servername = "localhost";
    $dbUsername = "root";
    $password = "";
    $dbname = "Movdle";

    $conn = new mysqli($servername, $dbUsername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if user exists for this movie
    $checkUserSql = "SELECT * FROM Users WHERE Username = '$username' AND movie_name = '$movie_name'";
    $result = $conn->query($checkUserSql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) {
            $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$username' AND movie_name = '$movie_name'";
            $conn->query($updateSql);
        }
    } else {
        $sql = "INSERT INTO Users (Username, Succesful_attempt, movie_name) VALUES ('$username', '$attempts', '$movie_name')";
        $conn->query($sql);
    }

    $conn->close();
    return true;
}
?>