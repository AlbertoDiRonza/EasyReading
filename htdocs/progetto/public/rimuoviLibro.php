<?php
header('Content-Type: application/json');
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $idLibro = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM libro WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Errore nella preparazione della query: " . $conn->error);
        }
        $stmt->bind_param("i", $idLibro);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Libro rimosso con successo!']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Errore durante la rimozione del libro: ' . $e->getMessage()]);
    }
}
?>
