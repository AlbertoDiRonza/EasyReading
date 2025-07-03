<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

header('Content-Type: application/json');

try {
    // Verifica il metodo della richiesta
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metodo non consentito", 405);
    }

    // Verifica la sessione attiva
    if (!isset($_SESSION['idUtente'])) {
        throw new Exception("Richiesta non autenticata", 401);
    }

    $userId = $_SESSION['idUtente'];

    // Disabilita i vincoli di foreign key
    $conn->query('SET FOREIGN_KEY_CHECKS = 0');

    // Elimina tutte le entità collegate
    $tables = ['libro', 'obiettivi', 'statistiche'];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE idUtente = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'eliminazione dei dati correlati", 500);
        }
    }

    // Elimina l'utente
    $stmt = $conn->prepare("DELETE FROM utente WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        throw new Exception("Errore nell'eliminazione dell'utente", 500);
    }

    // Riabilita i vincoli
    $conn->query('SET FOREIGN_KEY_CHECKS = 1');

    // Distruggi la sessione
    session_unset();
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Account eliminato con successo'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'errorCode' => $e->getCode()
    ]);
    exit;
}
?>