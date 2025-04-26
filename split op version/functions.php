<?php
session_start();

// Function finder en random poster fra dir
function getRandomMoviePoster($folder = 'Movie_posters') {
    $images = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (count($images) > 0) {
        return $images[array_rand($images)];
    }
    return '';
}

// Function for at samligne target med guess
function compareGuess($guess, $target) {
    $result = [];
    $targetLetters = str_split($target);
    $guessLetters = str_split($guess);
    $targetLetterCount = array_count_values($targetLetters);

    // First pass: chekker for exat match for all bogstaver
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($guessLetters[$i] === $targetLetters[$i]) {
            $result[$i] = 'correct';  // rightig bogstaver i correct orden
            $targetLetterCount[$guessLetters[$i]]--;  // reducer count
        } else {
            $result[$i] = ''; // oof
        }
    }

    // Second pass: checkker om folk er ordblinde
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($result[$i] === '') {
            if (in_array($guessLetters[$i], $targetLetters) && $targetLetterCount[$guessLetters[$i]] > 0) {
                $result[$i] = 'misplaced'; // Correct letter, wrong spot
                $targetLetterCount[$guessLetters[$i]]--;
            } else {
                $result[$i] = 'incorrect'; // Wrong letter
            }
        }
    }

    return $result;
}
?>
