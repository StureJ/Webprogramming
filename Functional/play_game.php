<?php
session_start();

// Save username if posted
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Function to get a random image from the "Movie_posters" folder
function getRandomMoviePoster($folder = 'Movie_posters') {
    $images = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (count($images) > 0) {
        return $images[array_rand($images)];
    }
    return '';
}

// Set random poster and target word if not already set
if (!isset($_SESSION['random_poster'])) {
    $randomPosterPath = getRandomMoviePoster();
    $_SESSION['random_poster'] = $randomPosterPath;

    $filename = pathinfo($randomPosterPath, PATHINFO_FILENAME);
    $_SESSION['target_word'] = strtolower($filename);
}

// Initialize attempts and guesses
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

if (!isset($_SESSION['guesses'])) {
    $_SESSION['guesses'] = [];
}

$randomPoster = $_SESSION['random_poster'] ?? '';

// Handle guess submission
if (isset($_POST['guess'])) {
    $guess = strtolower(trim($_POST['guess']));
    $target = strtolower($_SESSION['target_word']);

    // Add the guess to the guesses array
    $_SESSION['guesses'][] = $guess;

    // Increment attempts
    $_SESSION['attempts']++;

    if ($guess === $target) {
        echo "<p>✅ You guessed correctly in " . $_SESSION['attempts'] . " attempts!</p>";

        // Save result to database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Movdle";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user = $_SESSION['username'];
        $attempts = $_SESSION['attempts'];

        // Check if user already exists
        $checkUserSql = "SELECT * FROM Users WHERE Username = '$user'";
        $result = $conn->query($checkUserSql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) {
                $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$user'";
                $conn->query($updateSql);
            }
        } else {
            $sql = "INSERT INTO Users (Username, Succesful_attempt) VALUES ('$user', '$attempts')";
            $conn->query($sql);
        }

        $conn->close();

        // Destroy the session to reset game
        session_destroy();

        // Refresh the page after 2 seconds
        echo "<script>
            setTimeout(function() {
                window.location.href = window.location.href;
            }, 2000);
        </script>";

    } else {
        // Wrong guess
        echo "<p>❌ Wrong guess, try again! You have " . (5 - $_SESSION['attempts']) . " attempts left.</p>";

        if ($_SESSION['attempts'] >= 5) {
            echo "<p>❌ You've used all attempts. The correct answer was: <strong>" . htmlspecialchars($_SESSION['target_word']) . "</strong>.</p>";

            // Destroy the session to reset game
            session_destroy();

            // Refresh the page after 2 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
            </script>";
        }
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Random Movie Poster -->
<?php if ($randomPoster): ?>
    <div class="random-poster">
        <img src="<?php echo $randomPoster; ?>" alt="Random Movie Poster" style="max-width: 100%; height: auto; margin-bottom: 20px;">
    </div>
<?php endif; ?>

<!-- Leaderboard Section -->
<div class="leaderboard">
    <h2>Leaderboard</h2>
    <table id="leaderboard-table" border="1">
        <tr><th>Username</th><th>Successful Attempts</th></tr>
        <!-- Leaderboard will be populated by Ajax -->
    </table>
</div>

<!-- Display Previous Guesses -->
<div class="guesses">
    <h3>Your Guesses</h3>
    <ul>
        <?php
        foreach ($_SESSION['guesses'] as $guess) {
            echo "<li>" . htmlspecialchars($guess) . "</li>";
        }
        ?>
    </ul>
</div>

<!-- Game Input -->
<div class="input">
    <form method="POST">
        <input type="text" name="guess" placeholder="Enter your guess" required>
        <input type="submit" value="Guess">
    </form>
</div>

<script>
    // Function to load leaderboard
    function loadLeaderboard() {
        $.ajax({
            url: 'get_leaderboard.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var leaderboardHtml = "<tr><th>Username</th><th>Successful Attempts</th></tr>";
                data.forEach(function(item) {
                    leaderboardHtml += "<tr><td>" + item.Username + "</td><td>" + item.Succesful_attempt + "</td></tr>";
                });
                $('#leaderboard-table').html(leaderboardHtml);
            }
        });
    }

    // Update leaderboard after correct guess
    function updateLeaderboard() {
        loadLeaderboard();
    }

    $(document).ready(function() {
        loadLeaderboard();
        setInterval(updateLeaderboard, 5000);
    });
</script>

</body>
</html>
