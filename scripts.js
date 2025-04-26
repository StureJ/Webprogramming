//Pixeler billede
function pixelateImage(ctx, canvas, pixelationLevel) //billede, canvas og pixels
{
    var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height); //billede data
    var pixels = imgData.data; //gemt i variablen pixels
    var size = pixelationLevel * 10; //Hvor stor skal blocken være baseret på billedet.

    for (var y = 0; y < canvas.height; y += size) //
        {
        for (var x = 0; x < canvas.width; x += size) //Loop fra venstre til højre
            {
            var index = (x + y * canvas.width) * 4;
            var r = pixels[index];
            var g = pixels[index + 1];
            var b = pixels[index + 2];

            ctx.fillStyle = 'rgb(' + r + ',' + g + ',' + b + ')'; //Udregner farven
            ctx.fillRect(x, y, size, size); //Tegner en ny block med farven.
            }
    }
}

//Load billede og apply pixelering
function loadPosterImage(posterPath, attemptsCount, gameOver) 
{
    var img = new Image();
    img.crossOrigin = 'anonymous'; //Den bliver sendt uden information.
    img.src = posterPath;

    var canvas = document.getElementById('posterCanvas'); //finder HTML med navnet posterCanvas
    var ctx = canvas.getContext('2d'); //Tegne på canvas

    img.onload = function () 
    {
        var scaleFactor = 1;
        var scaledWidth = img.width * scaleFactor;
        var scaledHeight = img.height * scaleFactor;

        canvas.width = scaledWidth;
        canvas.height = scaledHeight;

        ctx.drawImage(img, 0, 0, scaledWidth, scaledHeight); //Tegner på billedet

        if (!gameOver) 
            {
            let pixelationLevel = Math.max(1, 5 - attemptsCount); //Udregner pixelation level udfra hvor mange gæt der er tilbage, færre = bedre kvalitet.
            pixelateImage(ctx, canvas, pixelationLevel); //Kalder funktionen fra før.
            }
    }
}

//Ajax funktion for updatere leaderboard
function loadLeaderboard() 
{
    $.ajax({ //Sender en HTTP request
        url: 'leaderboard.php', //Vores leaderboard script
        type: 'GET',
        dataType: 'json', //Den forventer det i JSON format, vi har omdannet fra før.
        success: function(data) 
        {
            var leaderboardHtml = "<tr><th>Username</th><th>Attempts used</th></tr>"; //HTML for leaderboard
            data.forEach(function(item) 
            {
                leaderboardHtml += "<tr><td>" + item.Username + "</td><td>" + item.Succesful_attempt + "</td></tr>";
            });
            $('#leaderboard-table').html(leaderboardHtml);
        }
    });
}

//Funtion der opdaterer leaderboard
function updateLeaderboard() 
{
    loadLeaderboard();
}

//Initalizer når DOM er klar.
$(document).ready(function() 
{
    loadLeaderboard();
    setInterval(updateLeaderboard, 3000); //Opdater leaderboardet hvert 3. sekund
    
    //Hvis der er "game over", reloader siden.
    if (typeof gameOver !== 'undefined' && gameOver) 
    {
        setTimeout(function() 
        {
            window.location.href = window.location.href;
        }, 3000);
    }
});