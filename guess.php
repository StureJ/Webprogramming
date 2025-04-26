<?php
session_start();
?>

 <!-- Display tidligere gæt -->
<div class="guesses">
    <h3>Your Guesses</h3>
    <ul>
        <?php
        //Display gæt fra denne session.
        if (isset($_SESSION['guesses'])) 
        {
            foreach ($_SESSION['guesses'] as $guess) 
            {
                echo "<li>" . htmlspecialchars($guess) . "</li>";
            }
        }
        ?>
    </ul>
</div>

<!--Input for gæt-->
<div class="input">
    <form method="POST" action="game.php">
        <input type="text" name="guess" placeholder="Enter your guess" required>
        <input type="submit" value="Guess">
    </form>
</div>


