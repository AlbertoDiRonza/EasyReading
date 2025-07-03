<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

if (!isset($_SESSION['idUtente'])) {
    die(json_encode(["error" => "Utente non autenticato"]));
}

$goalType = $_GET['type'] ?? $_GET['goalType'] ?? '';
$validTypes = ['pagineQuotidiane', 'libriAnnuali', 'pagineLibroCorrente'];
if (!in_array($goalType, $validTypes)) {
    die(json_encode(["error" => "Tipo obiettivo non valido"]));
}

function mapColumn($type) {
    return match($type) {
        'pagineQuotidiane' => 'pagineLetteOggi',
        'libriAnnuali' => 'libriLettiAnnualmente',
        'pagineLibroCorrente' => 'letturaLibroCorrente'
    };
}

try {
    $stmt = $conn->prepare("SELECT 
        o.$goalType AS goal,
        s." . mapColumn($goalType) . " AS current
    FROM obiettivi o
    JOIN statistiche s ON o.idUtente = s.idUtente
    WHERE o.idUtente = ?");
    $stmt->bind_param("i", $_SESSION['idUtente']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    echo json_encode([
        'current' => $result['current'],
        'goal' => $result['goal']
    ]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
