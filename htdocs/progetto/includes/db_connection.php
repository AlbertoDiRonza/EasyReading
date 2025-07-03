<?php
// Connessione al database
$servername = "localhost";
$username = "root";
$password = ""; // Lascia vuoto se non hai impostato una password in XAMPP
$dbname = "progetto_db";

// Creazione connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
