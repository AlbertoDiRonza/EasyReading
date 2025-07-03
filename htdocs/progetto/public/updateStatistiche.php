<?php
// updateStatistiche.php
ob_start();
try {
    session_start();
    require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metodo non consentito");
    }
    if (!isset($_SESSION['idUtente'])) {
        throw new Exception("Sessione scaduta");
    }
    $userId = $_SESSION['idUtente'];
    $pagineOggi  = isset($_POST['pagine-lette-oggi']) ? (int)$_POST['pagine-lette-oggi'] : 0;
    $pagineLibro = isset($_POST['pagine-libro-corrente']) ? (int)$_POST['pagine-libro-corrente'] : 0;

    // Avvia la transazione
    $conn->begin_transaction();

    // 1. Aggiornamento delle statistiche (resettando o sommando pagineLetteOggi a seconda della data)
    $stmt = $conn->prepare("UPDATE statistiche 
        SET pagineLetteOggi = IF(DATE(ultimoAggiornamento) < CURDATE(), ?, pagineLetteOggi + ?),
            letturalibroCorrente = ?,
            ultimoAggiornamento = NOW()
        WHERE idUtente = ?");
    if (!$stmt) {
        throw new Exception("Preparazione query statistiche fallita: " . $conn->error);
    }
    $stmt->bind_param("iiii", $pagineOggi, $pagineOggi, $pagineLibro, $userId);
    $stmt->execute();
    if ($stmt->errno) {
        throw new Exception("Errore nell'aggiornamento delle statistiche: " . $stmt->error);
    }

    // 2. Se è stato inserito il titolo, aggiunge il nuovo libro e aggiorna i contatori
    if (!empty($_POST['titolo'])) {
        $stmt = $conn->prepare("INSERT INTO libro 
            (titolo, editore, autore, genere, dataLettura, numeroPagine, dataPubblicazione, idUtente)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Preparazione query inserimento libro fallita: " . $conn->error);
        }
        $titolo            = $_POST['titolo'];
        $editore           = $_POST['editore'];
        $autore            = $_POST['autore'];
        $genere            = $_POST['genere'];
        $dataLettura       = $_POST['dataLettura'];
        $numeroPagine      = isset($_POST['numeroPagine']) ? (int)$_POST['numeroPagine'] : 0;
        $dataPubblicazione = $_POST['dataPubblicazione'];
        $stmt->bind_param("sssssisi", $titolo, $editore, $autore, $genere, $dataLettura, $numeroPagine, $dataPubblicazione, $userId);
        $stmt->execute();
        if ($stmt->errno) {
            throw new Exception("Errore nell'inserimento del libro: " . $stmt->error);
        }
        $stmt = $conn->prepare("UPDATE statistiche 
            SET libriLettiAnnualmente = libriLettiAnnualmente + 1,
                numeroLibri = numeroLibri + 1
            WHERE idUtente = ?");
        if (!$stmt) {
            throw new Exception("Preparazione query aggiornamento libri fallita: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if ($stmt->errno) {
            throw new Exception("Errore nell'aggiornamento dei contatori libri: " . $stmt->error);
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Statistiche aggiornate e libro inserito (se presente)!']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'ERRORE: ' . $e->getMessage()]);
} finally {
    ob_end_flush();
}
?>
