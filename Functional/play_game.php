<?php
session_start();

// Save username if posted
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Set a target word if not already set
if (!isset($_SESSION['target_word'])) {
    $_SESSION['target_word'] = "orange"; // You can change this or randomize later
}

// Initialize attempts count
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

// Handle guess submission
if (isset($_POST['guess'])) {
    $guess = strtolower(trim($_POST['guess']));
    $target = strtolower($_SESSION['target_word']);
    
    // Increment the attempt counter
    $_SESSION['attempts']++;

    if ($guess === $target) {
        echo "<p>✅ You guessed correctly in " . $_SESSION['attempts'] . " attempts!</p>";

        // Save result to database (lowest attempt before correct guess)
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Movdle";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user = $_SESSION['username'];
        $attempts = $_SESSION['attempts']; // Save the number of attempts

        // Check if user already exists in the database
        $checkUserSql = "SELECT * FROM Users WHERE Username = '$user'";
        $result = $conn->query($checkUserSql);

        if ($result->num_rows > 0) {
            // User exists, update attempts if current guess is better
            $row = $result->fetch_assoc();
            if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) {
                $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$user'";
                $conn->query($updateSql);
            }
        } else {
            // User doesn't exist, insert a new record
            $sql = "INSERT INTO Users (Username, Succesful_attempt) VALUES ('$user', '$attempts')";
            $conn->query($sql);
        }

        $conn->close();

        // Mark the user as having guessed correctly
        $_SESSION['guessed_correctly'] = true;

        // Trigger leaderboard update after correct guess
        echo "<script>updateLeaderboard();</script>";

        // Optionally destroy the session if you want to reset after success
        // session_destroy();
    } else {
        echo "<p>❌ Wrong guess, try again! You have " . (5 - $_SESSION['attempts']) . " attempts left.</p>";
        
        if ($_SESSION['attempts'] >= 5) {
            echo "<p>❌ You've used all attempts. Please refresh to try again.</p>";
        }
    }
}

// Function to get a random image from the "Movie_posters" folder
function getRandomMoviePoster($folder = 'Movie_posters') {
    $images = glob($folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if (count($images) > 0) {
        $randomImage = $images[array_rand($images)];
        return $randomImage;
    }
    return ''; // Return empty if no images found
}

$randomPoster = getRandomMoviePoster(); // Get a random poster
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
        <tr><th>Username</th><th>Score</th></tr>
        <!-- Leaderboard will be populated by Ajax -->
    </table>
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

    // Trigger leaderboard update after correct guess (you could add this after the result is saved in play_game.php)
    $(document).ready(function() {
        loadLeaderboard(); // Initial load of leaderboard
        // Set up an interval to refresh leaderboard
        setInterval(updateLeaderboard, 5000);
    });
</script>

</body>
</html>
