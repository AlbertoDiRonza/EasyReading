<?php
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Hash della password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Controllo se l'username esiste già
        $stmt = $conn->prepare("SELECT id FROM utente WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Errore: Username già in uso.";
        } else {
            // Creazione di un nuovo record nella tabella statistiche
            $conn->query("INSERT INTO statistiche () VALUES ()");
            $id_statistiche = $conn->insert_id; // Recupera l'ID appena creato

            // Inserimento dell'utente
            $stmt = $conn->prepare("INSERT INTO utente (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $userId = $stmt->insert_id; // Otteniamo l'ID dell'utente appena registrato
                $_SESSION['idUtente'] = $userId;  // Aggiunto

                // Associa l'utente alle statistiche
                $stmtUpdate = $conn->prepare("UPDATE statistiche SET idUtente = ? WHERE id = ?");
                $stmtUpdate->bind_param("ii", $userId, $id_statistiche);
                $stmtUpdate->execute();

                // Definiamo gli obiettivi di default
                $pagineQuotidiane = 15;
                $libriAnnuali = 3;
                $paginaLibroCorrente = 100;

                // Controlliamo se l'utente ha un libro associato per determinare il numero di pagine
                $sqlLibro = "SELECT numeroPagine FROM libro WHERE idUtente = ? ORDER BY dataLettura DESC LIMIT 1";
                $stmtLibro = $conn->prepare($sqlLibro);
                $stmtLibro->bind_param("i", $userId);
                $stmtLibro->execute();
                $resultLibro = $stmtLibro->get_result();

                if ($row = $resultLibro->fetch_assoc()) {
                    $paginaLibroCorrente = $row['numeroPagine'];
                }

                // Inseriamo gli obiettivi nel database
                $sqlObiettivi = "INSERT INTO obiettivi (pagineQuotidiane, libriAnnuali, pagineLibroCorrente, idUtente) 
                                 VALUES (?, ?, ?, ?)";
                $stmtObiettivi = $conn->prepare($sqlObiettivi);
                $stmtObiettivi->bind_param("iiii", $pagineQuotidiane, $libriAnnuali, $paginaLibroCorrente, $userId);
                $stmtObiettivi->execute();

                // Reindirizza l'utente alla home
                header("Location: home.php");
                exit();
            } else {
                echo "Errore durante la registrazione.";
            }
        }
        $stmt->close();
    } else {
        echo "Tutti i campi sono obbligatori.";
    }
    $conn->close();
}
?>