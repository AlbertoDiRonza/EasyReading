<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/bgStyle.css">

    <script src="../assets/js/bgScript.js" defer></script>
    
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="container text-center">
    <h1 class="mb-4 text-dark">Easy Reading</h1>
    <h2 class="h5 text-muted">Monitoraggio abitudini di lettura</h2>
        <div class="card shadow-lg p-4 rounded-lg" style="max-width: 400px; margin: auto;">
            <h2 class="h4 text-dark mb-3">Registrazione</h2>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Conferma Password</label>
                    <input type="password" id="confirm_password" class="form-control" required>
                    <div id="passwordError" class="text-danger mt-1" style="display: none;">Le password non coincidono.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrati</button>
            </form>
            <p class="mt-3">Hai già un account? <a href="login.html" class="text-primary">Accedi qui</a></p>
        </div>
    </div>
</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const passwordError = document.getElementById("passwordError");
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        if (password.value !== confirmPassword.value) {
            event.preventDefault(); // Blocca l'invio del form
            passwordError.style.display = "block";
        } else {
            passwordError.style.display = "none";
        }
    });
});
</script>