let intro = document.querySelector('.intro');
let logo = document.querySelector('.logo-header');
let logoSpan = document.querySelectorAll('.logo');
const inputContainer = document.getElementById("inputContainer")
const inputText = document.getElementById("inputText");
const inputBox = document.getElementById("hiddenInput");
const guessHistory = document.getElementById("guessHistory");


window.addEventListener('DOMContentLoaded', () => {

    setTimeout(() => {

        logoSpan.forEach((span, idx) => {
            setTimeout(() => {
                span.classList.add('active');
            }, (idx + 1) * 400);
        });
        
        setTimeout(() => {
            logoSpan.forEach((span, idx) => {
                setTimeout(() => {
                    span.classList.remove('active');
                    span.classList.add('fade');
                }, (idx + 1) * 50);
            });
        }, 2000);

        setTimeout(() => {
            intro.style.top = '-100vh';
        }, 2300);
    }, 0);
});

document.addEventListener("keydown", (event) => {
    if (event.key.toLowerCase() === "r" && document.activeElement !== inputBox) {
        guessHistory.innerHTML = ""; // Clear guess history
        console.log("Guess history wiped!");
    }
});

document.getElementById("hiddenInput").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        let guess = this.value.trim();
        if (guess !== "") {
            let guessContainer = document.createElement("div");
            guessContainer.classList.add("guess-item", "typing-effect");
            
            document.getElementById("guessHistory").appendChild(guessContainer);

            typewriterEffect(guess, guessContainer);
            this.value = ""; // Clear input
        }
    }
});

function typewriterEffect(text, element) {
    let i = 0;
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, 50); // Adjust typing speed
        } else {
            element.classList.add("no-blink"); // Remove cursor after typing
        }
    }
    type();
}

function fetchMovieImage(movieTitle) {
    // Placeholder for AJAX request
    console.log("Fetching image for:", movieTitle);
    
    // Simulate setting an image after request
    setTimeout(() => {
        document.getElementById("movieImage").src = "https://via.placeholder.com/250";
        document.getElementById("movieImage").classList.remove("hidden");
    }, 1000);
}

// Trigger image fetch on guess submission
document.getElementById("hiddenInput").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        let guess = this.value.trim();
        if (guess !== "") {
            fetchMovieImage(guess);
        }
    }
});





