<?php
session_start();
include('functions.php');

// Save username if posted
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Handle winning/losing and session resetting first
if (isset($_SESSION['game_over']) && $_SESSION['game_over']) {
    unset($_SESSION['random_poster']);
    unset($_SESSION['target_word']);
    unset($_SESSION['attempts']);
    unset($_SESSION['guesses']);
    unset($_SESSION['game_over']);
    unset($_SESSION['won']);
}

// Set random poster and target word if not already set
if (!isset($_SESSION['random_poster'])) {
    $randomPosterPath = getRandomMoviePoster();
    $_SESSION['random_poster'] = $randomPosterPath;

    $filename = pathinfo($randomPosterPath, PATHINFO_FILENAME);
    $_SESSION['target_word'] = strtolower($filename);

    $_SESSION['attempts'] = 0;    // Reset attempts when new poster picked
    $_SESSION['guesses'] = [];    // Reset guesses too
}

$randomPoster = $_SESSION['random_poster'] ?? '';

// Handle guess submission
if (isset($_POST['guess'])) {
    $guess = strtolower(trim($_POST['guess']));
    $target = strtolower($_SESSION['target_word']);

    $_SESSION['guesses'][] = $guess;
    $comparisonResult = compareGuess($guess, $target);

    $_SESSION['attempts']++;

    if ($guess === $target) {
        $_SESSION['game_over'] = true;
        $_SESSION['won'] = true; // optional
        
        // Save to database
        $user = $_SESSION['username'];
        $attempts = $_SESSION['attempts'];
        $movie_name = $_SESSION['target_word'];
        saveWinToDatabase($user, $attempts, $movie_name);

    } else {
        if ($_SESSION['attempts'] >= 5) {
            $_SESSION['game_over'] = true;
            $_SESSION['won'] = false; // optional
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts.js"></script>
</head>
<body>

<!-- Random Movie Poster -->
<?php if ($randomPoster): ?>
    <div class="random-poster">
        <canvas id="posterCanvas" style="max-width: 100%; height: auto; margin-bottom: 20px;"></canvas>
    </div>

    <script>
        var randomPoster = "<?php echo $randomPoster; ?>";
        var attempts = <?php echo isset($_SESSION['attempts']) ? $_SESSION['attempts'] : 0; ?>;
        var gameOver = <?php echo isset($_SESSION['game_over']) && $_SESSION['game_over'] ? 'true' : 'false'; ?>;
        
        // Load the poster image and apply appropriate pixelation
        loadPosterImage(randomPoster, attempts, gameOver);
    </script>
<?php endif; ?>

<!-- Leaderboard Section -->
<div class="leaderboard">
    <h2>Leaderboard</h2>
    <table id="leaderboard-table" border="1">
        <tr><th>Username</th><th>Successful Attempts</th></tr>
    </table>
</div>

<!-- Display Previous Guesses -->
<div class="guesses">
    <h3>Your Guesses</h3>
    <ul>
        <?php
        if (isset($_SESSION['guesses'])) {
            foreach ($_SESSION['guesses'] as $guess) {
                $comparisonResult = compareGuess($guess, $_SESSION['target_word']);
                echo "<li>";
                for ($i = 0; $i < strlen($guess); $i++) {
                    $status = $comparisonResult[$i];
                    echo "<span class='$status'>" . htmlspecialchars($guess[$i]) . "</span>";
                }
                echo "</li>";
            }
        }
        ?>
    </ul>
</div>

<!-- Game Input -->
<div class="input">
    <form method="POST">
        <input type="text" name="guess" placeholder="Enter your guess" required maxlength="5">
        <input type="submit" value="Guess">
    </form>
</div>

</body>
</html>