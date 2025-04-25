var img = new Image();
img.crossOrigin = 'anonymous';
var imagePath = 'Alien.jpg';
img.src = imagePath;

var canvas = document.getElementById('canvas');
var ctx = canvas.getContext('2d');
var level = 2;
img.onload = function () {
    canvas.width = img.width;
    canvas.height = img.height;

    ctx.drawImage(img, 0, 0, img.width, img.height);

    pixelateImage(ctx, canvas, level); // Level 1-5
};

function pixelateImage(ctx, canvas, level) {
    const blockSize = level * 10; // Adjust block size (higher level = larger blocks)
    const width = canvas.width;
    const height = canvas.height;

    const imageData = ctx.getImageData(0, 0, width, height);
    const data = imageData.data;

    if (level > 0) {
        
    

    // Loop through each block
    for (let y = 0; y < height; y += blockSize) {
        for (let x = 0; x < width; x += blockSize) {
            let r = 0, g = 0, b = 0, count = 0;

            // Loop through each pixel in the block
            for (let yy = 0; yy < blockSize; yy++) {
                for (let xx = 0; xx < blockSize; xx++) {
                    let pixelX = x + xx;
                    let pixelY = y + yy;
                    if (pixelX < width && pixelY < height) { // Ensure within bounds
                        let index = (pixelY * width + pixelX) * 4;
                        r += data[index];
                        g += data[index + 1];
                        b += data[index + 2];
                        count++;
                    }
                }
            }

            // Compute average color for the block
            r = Math.floor(r / count);
            g = Math.floor(g / count);
            b = Math.floor(b / count);

            // Assign average color to all pixels in the block
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

    // Put modified image data back onto canvas
    ctx.putImageData(imageData, 0, 0);
}

