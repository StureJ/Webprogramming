<?php
session_start();
include('functions.php'); //Vi henter funktioner fra denne fil

//Post users navn
if (isset($_POST['username'])) 
{
    $_SESSION['username'] = $_POST['username'];
}

//Hvis de har gættet rigtigt eller tabt, resetter vi. 
if (isset($_SESSION['game_over']) && $_SESSION['game_over']) {
    unset($_SESSION['random_poster']);
    unset($_SESSION['target_word']);
    unset($_SESSION['attempts']);
    unset($_SESSION['guesses']);
    unset($_SESSION['game_over']);
    unset($_SESSION['won']);
}

//Random film + svar
if (!isset($_SESSION['random_poster'])) 
{
    $randomPosterPath = getRandomMoviePoster();
    $_SESSION['random_poster'] = $randomPosterPath; //Pathen til mappen

    $filename = pathinfo($randomPosterPath, PATHINFO_FILENAME); //Vi finder navnet på filen, som fungerer som svaret
    $_SESSION['target_word'] = strtolower($filename); //Laver til lowercase

    $_SESSION['attempts'] = 0;    //Reset svar hvis ny film bliver valgt
    $_SESSION['guesses'] = [];    //Reset gæt
}

$randomPoster = $_SESSION['random_poster'] ?? ''; //?? = er det set eller ikke

//Brugerens guess
if (isset($_POST['guess'])) 
{
    $guess = strtolower(trim($_POST['guess'])); //omdanner til lowercase fordi simpelt
    $target = strtolower($_SESSION['target_word']); 

    $_SESSION['guesses'][] = $guess; //Brugerens gæt
    $comparisonResult = compareGuess($guess, $target); //kalder funktionen, kig i functions.php

    $_SESSION['attempts']++; //Gæt + 1

    if ($guess === $target) 
    {
        $_SESSION['game_over'] = true;
        $_SESSION['won'] = true; 
        
        //Push til databasen
        $user = $_SESSION['username'];
        $attempts = $_SESSION['attempts'];
        $movie_name = $_SESSION['target_word'];
        saveWinToDatabase($user, $attempts, $movie_name); //Kalder funktionen saveWin, kig i functions.php

    } else 
    {
        if ($_SESSION['attempts'] >= 5) //Hvis de ikke kan gætte det
        {
            $_SESSION['game_over'] = true;
            $_SESSION['won'] = false; 
        }
    }
}
?>


<!-- HTML -->
<!DOCTYPE html> 
<html>
<head>
    <link rel="stylesheet" href="stylesheet.css"> <!-- Kalder stylesheet.css -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Kalder JQUERY -->
    <script src="scripts.js"></script> <!-- Kalder scripts.js -->
</head>
<body>

<!-- Movie Poster -->
<?php if ($randomPoster): ?> <!-- Tjek at den er valgt -->
    <div class="random-poster">
        <canvas id="posterCanvas" style="max-width: 100%; height: auto; margin-bottom: 20px;"></canvas> <!-- Laver posteren i canvas, fordi vi vil pixelate senere -->
    </div>

    <script>
        var randomPoster = "<?php echo $randomPoster; ?>"; //fra php til js value
        var attempts = <?php echo isset($_SESSION['attempts']) ? $_SESSION['attempts'] : 0; ?>; //Brugerens forsøg, hvis ingen = 0
        var gameOver = <?php echo isset($_SESSION['game_over']) && $_SESSION['game_over'] ? 'true' : 'false'; ?>; //Tjek om game er færdigt.
        
        //Vi loader posteren og pixelerer den
        loadPosterImage(randomPoster, attempts, gameOver); //Kalder funktionen fra scripts.js
    </script>
<?php endif; ?>

<!-- Leaderboard -->
<div class="leaderboard">
    <h2>Leaderboard</h2>
    <table id="leaderboard-table" border="1">
        <tr><th>Username</th><th>Attempts used</th></tr>
    </table>
</div>

 <!-- Tidligere svar -->
<div class="guesses">
    <h3>Your Guesses</h3>
    <ul>  <!-- Unordered list-->
        <?php
        if (isset($_SESSION['guesses'])) //tjekker guesses er set
        {
            foreach ($_SESSION['guesses'] as $guess) //foreach loop
            {
                $comparisonResult = compareGuess($guess, $_SESSION['target_word']); //kalder funktionen compareGuess, brugerens svar og rigtige svar.
                echo "<li>"; //Ny liste for nuværende gæt
                for ($i = 0; $i < strlen($guess); $i++) //Går igennem hvert bogstav.
                {
                    $status = $comparisonResult[$i]; //Correct, misplaced, incorrect.
                    echo "<span class='$status'>" . htmlspecialchars($guess[$i]) . "</span>"; //Box rundt om hvert bogstav.
                }
                echo "</li>";
            }
        }
        ?>
    </ul>
</div>

<!-- Input -->
<div class="input">
    <form method="POST">
        <input type="text" name="guess" placeholder="Enter your guess" required maxlength="5"> <!-- Maks længde på 5 characters-->
        <input type="submit" value="Guess">
    </form>
</div>

</body>
</html>