document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logoutButton");

    logoutButton.addEventListener("click", function () {
        fetch("logout.php", {
            method: "GET",
            credentials: "same-origin" // Importante per mantenere la sessione
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url; // Reindirizza a login.html
            } else {
                console.error("Errore durante il logout.");
            }
        })
        .catch(error => console.error("Errore durante il logout:", error));
    });
});
