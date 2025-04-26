<?php
session_start();

// Save username
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}

// Setup the "correct answer"
$correctAnswer = "apple"; // you can randomize this if you want
$maxAttempts = 5;

// Check if the game is in progress
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

// Handle form submit
$message = "";
if (isset($_POST['guess'])) {
    $_SESSION['attempts']++;
    $guess = $_POST['guess'];

    if (strtolower($guess) == strtolower($correctAnswer)) {
        $message = "Correct! You guessed it in " . $_SESSION['attempts'] . " attempts.";
        header("Location: save_result.php?success=" . $_SESSION['attempts']);
        exit();
    } else {
        if ($_SESSION['attempts'] >= $maxAttempts) {
            $message = "Game Over. You used all 5 attempts.";
            header("Location: save_result.php?success=0"); // 0 means not successful
            exit();
        } else {
            $message = "Wrong! Try again.";
        }
    }
}
?>

<html>
<body>

<h2>Hello <?php echo $_SESSION['username']; ?>, guess the word!</h2>

<form method="POST">
  Your Guess: <input type="text" name="guess" required>
  <input type="submit" value="Guess">
</form>

<p><?php echo $message; ?></p>
<p>Attempt <?php echo $_SESSION['attempts']; ?> / <?php echo $maxAttempts; ?></p>

</body>
</html>
