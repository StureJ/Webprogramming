<?php
session_start();
include('functions.php');

// Save username if posted
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Set random poster and target word if not already set
if (!isset($_SESSION['random_poster'])) {
    $randomPosterPath = getRandomMoviePoster();
    $_SESSION['random_poster'] = $randomPosterPath;

    $filename = pathinfo($randomPosterPath, PATHINFO_FILENAME);
    $_SESSION['target_word'] = strtolower($filename);

    $_SESSION['attempts'] = 0;
    $_SESSION['guesses'] = [];
    $_SESSION['game_over'] = false;
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
        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'Movdle');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user = $_SESSION['username'] ?? 'Unknown';
        $attempts = $_SESSION['attempts'];
        $movie_name = $_SESSION['target_word'];

        $checkUserSql = "SELECT * FROM Users WHERE Username = '$user' AND movie_name = '$movie_name'";
        $result = $conn->query($checkUserSql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) {
                $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$user' AND movie_name = '$movie_name'";
                $conn->query($updateSql);
            }
        } else {
            $sql = "INSERT INTO Users (Username, Succesful_attempt, movie_name) VALUES ('$user', '$attempts', '$movie_name')";
            $conn->query($sql);
        }

        $conn->close();

        // Reset after success
        unset($_SESSION['random_poster'], $_SESSION['target_word'], $_SESSION['attempts'], $_SESSION['guesses'], $_SESSION['game_over']);

        echo "<script>
            setTimeout(function() {
                window.location.href = window.location.href;
            }, 3000);
        </script>";

    } else {
        if ($_SESSION['attempts'] >= 5) {
            session_destroy();
            echo "<script>
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 3000);
            </script>";
        }
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        window.randomPoster = "<?php echo $randomPoster; ?>";
        window.attempts = <?php echo $_SESSION['attempts'] ?? 0; ?>;
    </script>

    <script src="script.js"></script>
</head>
<body>

<!-- Random Movie Poster -->
<?php if ($randomPoster): ?>
    <div class="random-poster">
        <canvas id="posterCanvas" style="max-width: 100%; height: auto; margin-bottom: 20px;"></canvas>
    </div>
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
