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
?>
