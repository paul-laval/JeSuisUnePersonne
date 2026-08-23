function displayExpirationTime(delay) {
    delay -= 1000;
    if (delay < 0) {
        document.getElementById("expire").textContent = "Lien expiré";
    }
    else {
        const minElapsed = Math.floor((delay % (1000 * 60 * 60)) / (1000 * 60));
        const secElapsed = Math.floor((delay % (1000 * 60)) / 1000);
        document.getElementById("expire").textContent = "Expire dans " + String(minElapsed).padStart(2, '0') + ":" + String(secElapsed).padStart(2, '0');
    }
    return delay;
}

function openSource() {
    window.open("https://github.com/paul-laval/JeSuisUnePersonne");
}
