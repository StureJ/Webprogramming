<?php
session_start();

// Save username if posted
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Set a target word if not already set
if (!isset($_SESSION['target_word'])) {
    $_SESSION['target_word'] = "orange"; // <-- You can change this or randomize later
}

// Handle guess submission
if (isset($_POST['guess'])) {
    $guess = strtolower(trim($_POST['guess']));
    $target = strtolower($_SESSION['target_word']);

    if ($guess === $target) {
        echo "<p>✅ You guessed correctly!</p>";

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
        $success = 1; // you can change this if you want number of attempts etc.

        $sql = "INSERT INTO Users (Username, Succesful_attempt) VALUES ('$user', '$success')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Result saved successfully!</p>";
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        $conn->close();

        // Optionally destroy the session if you want to reset after success
        session_destroy();
    } else {
        echo "<p>❌ Wrong guess, try again!</p>";
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Leaderboard Section -->
<div class="leaderboard">
    <h2>Leaderboard</h2>
    <table id="leaderboard-table" border="1">
        <tr><th>Username</th><th>Successful Attempts</th></tr>
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

    // Update leaderboard every 5 seconds
    setInterval(loadLeaderboard, 5000);

    // Load leaderboard immediately on page load
    $(document).ready(function() {
        loadLeaderboard();
    });
</script>

</body>
</html>
