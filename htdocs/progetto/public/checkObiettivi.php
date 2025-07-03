<?php
ob_start();
try {
    session_start();
    require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
    
    if (!isset($_SESSION['idUtente'])) {
        throw new Exception("Sessione scaduta");
    }

    $stmt = $conn->prepare("SELECT pagineQuotidiane, pagineLibroCorrente FROM obiettivi WHERE idUtente = ?");
    $stmt->bind_param("i", $_SESSION['idUtente']);
    $stmt->execute();
    $result = $stmt->get_result();
    $obiettivi = $result->fetch_assoc();

    if (!$obiettivi) {
        throw new Exception("Nessun obiettivo configurato");
    }

    $response = [
        'validi' => true,
        'pagineQuotidiane' => $obiettivi['pagineQuotidiane'],
        'pagineLibroCorrente' => $obiettivi['pagineLibroCorrente']
    ];

} catch (Exception $e) {
    $response = [
        'validi' => false,
        'message' => $e->getMessage()
    ];
}

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>