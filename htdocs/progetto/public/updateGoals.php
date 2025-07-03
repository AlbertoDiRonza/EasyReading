<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['idUtente'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['idUtente'];
$type = $_POST['goalType'];  // Modifica da $_POST['type']
$value = (int)$_POST['newValue'];

// Validazione input
$allowedTypes = ['pagineQuotidiane', 'libriAnnuali', 'pagineLibroCorrente'];
if (!in_array($type, $allowedTypes) || $value < 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // 1. Recupera il vecchio obiettivo e i progressi attuali
    $conn->begin_transaction();
    
    $stmt = $conn->prepare("
        SELECT o.$type AS old_goal, s.* 
        FROM obiettivi o 
        JOIN statistiche s ON o.idUtente = s.idUtente 
        WHERE o.idUtente = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    // 2. Calcola il nuovo progresso proporzionale
    $oldProgress = match($type) {
        'pagineQuotidiane' => $data['pagineLetteOggi'],
        'libriAnnuali' => $data['libriLettiAnnualmente'],
        'pagineLibroCorrente' => $data['letturaLibroCorrente'] // Corretto typo qui
    };
    
    $newProgress = ($data['old_goal'] > 0) 
        ? round(($oldProgress / $data['old_goal']) * $value)
        : 0;

    // 3. Aggiorna obiettivo
    $stmt = $conn->prepare("UPDATE obiettivi SET $type = ? WHERE idUtente = ?");
    $stmt->bind_param("ii", $value, $userId);
    $stmt->execute();
    
    // 4. Aggiorna statistiche
    $statColumn = match($type) {
        'pagineQuotidiane' => 'pagineLetteOggi',
        'libriAnnuali' => 'libriLettiAnnualmente',
        'pagineLibroCorrente' => 'letturaLibroCorrente'
    };
    
    $stmt = $conn->prepare("UPDATE statistiche SET $statColumn = ? WHERE idUtente = ?");
    $stmt->bind_param("ii", $newProgress, $userId);
    $stmt->execute();
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'newGoal' => $value,
        'newProgress' => $newProgress
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
// Rimosso codice fuori dal tag PHP che causava l'errore
?>