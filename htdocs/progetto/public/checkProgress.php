<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['idUtente'])) {
    echo json_encode(['error' => 'Utente non autenticato']);
    exit;
}
$userId = $_SESSION['idUtente'];

$stmt = $conn->prepare("SELECT 
    (s.pagineLetteOggi / o.pagineQuotidiane * 100) AS pagineQuotidiane,
    (s.libriLettiAnnualmente / o.libriAnnuali * 100) AS libriAnnuali,
    (s.letturaLibroCorrente / o.pagineLibroCorrente * 100) AS pagineLibroCorrente
FROM statistiche s
JOIN obiettivi o ON s.idUtente = o.idUtente
WHERE s.idUtente = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
echo json_encode($result);
?>
