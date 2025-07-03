<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['idUtente'])) {
    echo json_encode(['error' => 'Utente non autenticato']);
    exit;
}
$userId = $_SESSION['idUtente'];
$year = isset($_GET['year']) ? (int)$_GET['year'] : date("Y");

$monthlyData = array_fill(0, 12, 0);
$stmt = $conn->prepare("SELECT MONTH(dataLettura) AS month, COUNT(*) AS count 
    FROM libro 
    WHERE idUtente = ? AND YEAR(dataLettura) = ? 
    GROUP BY MONTH(dataLettura)");
$stmt->bind_param("ii", $userId, $year);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $monthlyData[$row['month'] - 1] = (int)$row['count'];
}
echo json_encode($monthlyData);
?>
