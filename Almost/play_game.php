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

    $_SESSION['guesses'][] = $guess;
    $comparisonResult = compareGuess($guess, $target);

    $_SESSION['attempts']++;

    if ($guess === $target) {
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
        $movie_name = $_SESSION['target_word']; // Store the current movie name

        // Check if user exists for this movie
        $checkUserSql = "SELECT * FROM Users WHERE Username = '$user' AND movie_name = '$movie_name'";
        $result = $conn->query($checkUserSql);

        if ($result->num_rows > 0) {
            // If the user exists for this movie, update their successful attempt if it's fewer
            $row = $result->fetch_assoc();
            if ($row['Succesful_attempt'] == 0 || $attempts < $row['Succesful_attempt']) {
                $updateSql = "UPDATE Users SET Succesful_attempt = '$attempts' WHERE Username = '$user' AND movie_name = '$movie_name'";
                $conn->query($updateSql);
            }
        } else {
            // If the user doesn't exist for this movie, insert new entry
            $sql = "INSERT INTO Users (Username, Succesful_attempt, movie_name) VALUES ('$user', '$attempts', '$movie_name')";
            $conn->query($sql);
        }

        $conn->close();

        unset($_SESSION['random_poster']);
        unset($_SESSION['target_word']);
        unset($_SESSION['attempts']);
        unset($_SESSION['guesses']);


        echo "<script>
            setTimeout(function() {
                window.location.href = window.location.href;
            }, 2000);
        </script>";

    } else {
        if ($_SESSION['attempts'] >= 5) {
            session_destroy();

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
        <canvas id="posterCanvas" style="max-width: 100%; height: auto; margin-bottom: 20px;"></canvas>
    </div>

    <script>
        var randomPoster = "<?php echo $randomPoster; ?>";
        var attempts = <?php echo isset($_SESSION['attempts']) ? $_SESSION['attempts'] : 0; ?>;

        var img = new Image();
        img.crossOrigin = 'anonymous';
        img.src = randomPoster;

        var canvas = document.getElementById('posterCanvas');
        var ctx = canvas.getContext('2d');

        img.onload = function () {
            var scaleFactor = 1; // you can change this if you want to scale down
            var scaledWidth = img.width * scaleFactor;
            var scaledHeight = img.height * scaleFactor;

            canvas.width = scaledWidth;
            canvas.height = scaledHeight;

            ctx.drawImage(img, 0, 0, scaledWidth, scaledHeight);

            let pixelationLevel = Math.max(1, 5 - attempts);
            pixelateImage(ctx, canvas, pixelationLevel);
        }

        function pixelateImage(ctx, canvas, pixelationLevel) {
            var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            var pixels = imgData.data;
            var size = pixelationLevel * 10;

            for (var y = 0; y < canvas.height; y += size) {
                for (var x = 0; x < canvas.width; x += size) {
                    var index = (x + y * canvas.width) * 4;
                    var r = pixels[index];
                    var g = pixels[index + 1];
                    var b = pixels[index + 2];

                    ctx.fillStyle = 'rgb(' + r + ',' + g + ',' + b + ')';
                    ctx.fillRect(x, y, size, size);
                }
            }
        }
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
        foreach ($_SESSION['guesses'] as $guess) {
            $comparisonResult = compareGuess($guess, $_SESSION['target_word']);
            echo "<li>";
            for ($i = 0; $i < strlen($guess); $i++) {
                $status = $comparisonResult[$i];
                echo "<span class='$status'>" . htmlspecialchars($guess[$i]) . "</span>";
            }
            echo "</li>";
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

<script>
    function loadLeaderboard() {
        $.ajax({
            url: 'get_leaderboard.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var leaderboardHtml = "<tr><th>Username</th><th>Attempts used</th></tr>";
                data.forEach(function(item) {
                    leaderboardHtml += "<tr><td>" + item.Username + "</td><td>" + item.Succesful_attempt + "</td></tr>";
                });
                $('#leaderboard-table').html(leaderboardHtml);
            }
        });
    }

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
