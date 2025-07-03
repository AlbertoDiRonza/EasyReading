<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Preparazione della query
        $stmt = $conn->prepare("SELECT id, password FROM utente WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Controlla se l'utente esiste
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verifica della password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['idUtente'] = $id;  // Cambiato per uniformità con updateStatistiche.php
                $_SESSION['username'] = $username;

                header("Location: home.php");
                exit();
            } else {
                echo "<script>alert('Password errata!'); window.location.href = 'login.html';</script>";
            }
        } else {
            echo "<script>alert('Username non trovato!'); window.location.href = 'login.html';</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Compila tutti i campi!'); window.location.href = 'login.html';</script>";
    }
}
?>
