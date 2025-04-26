// Function to pixelate the canvas image
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

// Function to load the movie poster image and apply pixelation
function loadPosterImage(posterPath, attemptsCount, gameOver) {
    var img = new Image();
    img.crossOrigin = 'anonymous';
    img.src = posterPath;

    var canvas = document.getElementById('posterCanvas');
    var ctx = canvas.getContext('2d');

    img.onload = function () {
        var scaleFactor = 1;
        var scaledWidth = img.width * scaleFactor;
        var scaledHeight = img.height * scaleFactor;

        canvas.width = scaledWidth;
        canvas.height = scaledHeight;

        ctx.drawImage(img, 0, 0, scaledWidth, scaledHeight);

        if (!gameOver) {
            let pixelationLevel = Math.max(1, 5 - attemptsCount);
            pixelateImage(ctx, canvas, pixelationLevel);
        }
    }
}

// Function to load the leaderboard data
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

// Function to periodically update the leaderboard
function updateLeaderboard() {
    loadLeaderboard();
}

// Initialize the page when DOM is ready
$(document).ready(function() {
    loadLeaderboard();
    setInterval(updateLeaderboard, 5000);
    
    // If there's a game over, reload the page after 5 seconds
    if (typeof gameOver !== 'undefined' && gameOver) {
        setTimeout(function() {
            window.location.href = window.location.href;
        }, 5000);
    }
});