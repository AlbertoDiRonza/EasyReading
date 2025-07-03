<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['idUtente'])) {
    echo json_encode(['error' => 'Utente non autenticato']);
    exit;
}
$userId = $_SESSION['idUtente'];
$stmt = $conn->prepare("SELECT genere, COUNT(*) as count FROM libro WHERE idUtente = ? GROUP BY genere");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$labels = [];
$data = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['genere'];
    $data[] = (int)$row['count'];
}
echo json_encode(['labels' => $labels, 'data' => $data]);
?>
