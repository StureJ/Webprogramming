<?php
session_start();
?>

<!-- Display Previous Guesses -->
<div class="guesses">
    <h3>Your Guesses</h3>
    <ul>
        <?php
        // Display guesses from the session
        if (isset($_SESSION['guesses'])) {
            foreach ($_SESSION['guesses'] as $guess) {
                echo "<li>" . htmlspecialchars($guess) . "</li>";
            }
        }
        ?>
    </ul>
</div>

<!-- Game Input Form -->
<div class="input">
    <form method="POST" action="game.php">
        <input type="text" name="guess" placeholder="Enter your guess" required>
        <input type="submit" value="Guess">
    </form>
</div>

function evaluateGuess($guess, $target) {
    $result = [];

    // Convert the guess and target to arrays to compare each character
    $guessArray = str_split($guess);
    $targetArray = str_split($target);

    // First pass: Check for exact matches (green)
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($guessArray[$i] === $targetArray[$i]) {
            $result[$i] = 'green';
            $targetArray[$i] = null;  // Mark the character as matched
        }
    }

    // Second pass: Check for incorrect position (yellow)
    for ($i = 0; $i < strlen($guess); $i++) {
        if (!isset($result[$i])) {  // If not already matched (green)
            if (($key = array_search($guessArray[$i], $targetArray)) !== false) {
                $result[$i] = 'yellow';
                $targetArray[$key] = null;  // Mark this character as used
            } else {
                $result[$i] = 'gray';  // Incorrect letter (gray)
            }
        }
    }

    return $result;
}

