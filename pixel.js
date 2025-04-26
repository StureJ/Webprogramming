// pixel.js


var img = new Image();
img.crossOrigin = 'anonymous';
img.src = randomPoster;

var canvas = document.getElementById('canvas');
var ctx = canvas.getContext('2d');

var scaleFactor = 0.5;

img.onload = function () {
    var scaledWidth = img.width * scaleFactor;
    var scaledHeight = img.height * scaleFactor;

    canvas.width = scaledWidth;
    canvas.height = scaledHeight;

    ctx.drawImage(img, 0, 0, scaledWidth, scaledHeight);

    let pixelationLevel = Math.max(1, 5 - attempts);
    pixelateImage(ctx, canvas, pixelationLevel);
};

function pixelateImage(ctx, canvas, level) {
    const blockSize = level * 5;
    const width = canvas.width;
    const height = canvas.height;

    const imageData = ctx.getImageData(0, 0, width, height);
    const data = imageData.data;

    if (level > 0) {
        for (let y = 0; y < height; y += blockSize) {
            for (let x = 0; x < width; x += blockSize) {
                let r = 0, g = 0, b = 0, count = 0;
                for (let yy = 0; yy < blockSize; yy++) {
                    for (let xx = 0; xx < blockSize; xx++) {
                        let pixelX = x + xx;
                        let pixelY = y + yy;
                        if (pixelX < width && pixelY < height) {
                            let index = (pixelY * width + pixelX) * 4;
                            r += data[index];
                            g += data[index + 1];
                            b += data[index + 2];
                            count++;
                        }
                    }
                }

                r = Math.floor(r / count);
                g = Math.floor(g / count);
                b = Math.floor(b / count);

                for (let yy = 0; yy < blockSize; yy++) {
                    for (let xx = 0; xx < blockSize; xx++) {
                        let pixelX = x + xx;
                        let pixelY = y + yy;
                        if (pixelX < width && pixelY < height) {
                            let index = (pixelY * width + pixelX) * 4;
                            data[index] = r;
                            data[index + 1] = g;
                            data[index + 2] = b;
                        }
                    }
                }
            }
        }
    }

    ctx.putImageData(imageData, 0, 0);
}




