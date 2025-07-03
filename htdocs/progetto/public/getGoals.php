<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idUtente'])) {
    die("Errore: Utente non autenticato.");
}
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

$userId = $_SESSION['idUtente'];
$sqlObiettivi = "SELECT pagineQuotidiane, libriAnnuali, pagineLibroCorrente 
                 FROM obiettivi 
                 WHERE idUtente = ?";
$stmt = $conn->prepare($sqlObiettivi);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Crea record iniziale se non esiste
    $insert = $conn->prepare("INSERT INTO obiettivi (pagineQuotidiane, libriAnnuali, pagineLibroCorrente, idUtente)
                              VALUES (15, 3, 100, ?)");
    $insert->bind_param("i", $userId);
    $insert->execute();
    
    $goal = [
        "pagineQuotidiane" => 15,
        "libriAnnuali" => 3,
        "pagineLibroCorrente" => 100
    ];
} else {
    $goal = $result->fetch_assoc();
}
?>
