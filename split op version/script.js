var randomPoster = window.randomPoster;
var attempts = window.attempts;

// Pixlat den valgte poster
var img = new Image();
img.crossOrigin = 'anonymous';
img.src = randomPoster;

var canvas = document.getElementById('posterCanvas');
var ctx = canvas.getContext('2d');

img.onload = function () {
    var scaleFactor = 1;
    var scaledWidth = img.width * scaleFactor;
    var scaledHeight = img.height * scaleFactor;

    canvas.width = scaledWidth;
    canvas.height = scaledHeight;

    ctx.drawImage(img, 0, 0, scaledWidth, scaledHeight);

    let pixelationLevel = Math.max(1, 5 - attempts);

    if (pixelationLevel < 5) {
        pixelateImage(ctx, canvas, pixelationLevel);
    }
};

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

// Leaderboard function
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