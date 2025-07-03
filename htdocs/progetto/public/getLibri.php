<?php
session_start();
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

if (!isset($_SESSION['idUtente'])) {
    die(json_encode(["error" => "Utente non autenticato"]));
}

$userId = $_SESSION['idUtente'];

try {
    $stmt = $conn->prepare("SELECT * FROM libro WHERE idUtente = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $libri = [];
    while ($row = $result->fetch_assoc()) {
        $libri[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($libri);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>