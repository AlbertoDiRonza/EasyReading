<?php
session_start();
if (!isset($_SESSION['idUtente'])) {
    header("Location: login.php");
    exit();
}
require 'C:\xampp\htdocs\progetto\includes\db_connection.php';

// Recupera (o crea) gli obiettivi
require 'getGoals.php';

// Recupero statistiche e calcolo percentuali
$stmt = $conn->prepare("SELECT 
    s.pagineLetteOggi, 
    s.libriLettiAnnualmente, 
    s.letturaLibroCorrente
FROM statistiche s
WHERE s.idUtente = ?");
$stmt->bind_param("i", $_SESSION['idUtente']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Calcolo percentuali
$percentualeOggi = ($goal['pagineQuotidiane'] > 0) ? 
    min(round(($stats['pagineLetteOggi'] / $goal['pagineQuotidiane'] * 100)), 100) : 0;
$percentualeAnnuale = ($goal['libriAnnuali'] > 0) ? 
    min(round(($stats['libriLettiAnnualmente'] / $goal['libriAnnuali'] * 100)), 100) : 0;
$percentualeLibro = ($goal['pagineLibroCorrente'] > 0) ? 
    min(round(($stats['letturaLibroCorrente'] / $goal['pagineLibroCorrente'] * 100)), 100) : 0;
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- CSS personalizzato -->
  <link rel="stylesheet" href="../assets/css/homeStyle.css">
  
  <!-- jQuery (se necessario) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  
  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Dati utente globali
    const userData = {
        id: <?= json_encode($_SESSION['idUtente']) ?>,
        goals: <?= json_encode($goal) ?>,
        progress: {
            daily: <?= $percentualeOggi ?>,
            annual: <?= $percentualeAnnuale ?>,
            book: <?= $percentualeLibro ?>
        }
    };
  </script>

  <!-- Script personalizzati -->
  <script src="../assets/js/logout.js" defer></script>
  <script src="../assets/js/dltAccount.js" defer></script>
  <script src="../assets/js/histogram1.js" defer></script>
  <script src="../assets/js/histogram2.js" defer></script>
  <script src="../assets/js/tooltip.js" defer></script>
  <script src="../assets/js/scriptModali.js" defer></script>
  <script src="../assets/js/scrptMdlAggiornaProgressi.js" defer></script>
  <script src="../assets/js/ricercaISBN.js" defer></script>
  <script src="../assets/js/progressUpdater.js" defer></script>
</head>
<body>

<!-- Header a larghezza piena -->
<header class="container-fluid py-2">
  <div class="row align-items-center">
    <!-- Titolo allineato a sinistra -->
    <div class="col-12 col-md-6 text-start">
      <h1 class="m-0 text-header" style="font-size: 1.8rem;">Easy Reading</h1>
    </div>
    <div class="col-12 col-md-6 text-center text-md-end">
  <!-- Icona utente con tendina -->
  <div class="dropdown d-inline-block me-3"> <!-- Aggiunto me-3 per margine a destra -->
    <button class="btn btn-user" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fas fa-user"></i> <!-- Icona FontAwesome -->
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
      <li><a href="listaLibri.php" class="dropdown-item" id="showBookList">Mostra lista libri</a></li>
      <li><button class="dropdown-item text-danger" id="deleteAccount">Elimina account</button></li>
    </ul>
  </div>
  <!-- Pulsante Logout -->
  <button id="logoutButton" class="btn btn-logout btn-primary d-inline-block">
    <i class="fas fa-sign-out-alt"></i> Logout
  </button>
    </div>
  </div>
  <div class="row align-items-center mt-1">
    <div class="col-12 col-md-6 text-start">
      <h3 class="m-0" style="font-size: 1rem; color: #C7D66D;">Monitoraggio abitudini di lettura</h3>
    </div>
  </div>
</header>
<!-- Sezione statistiche, progress bar e modali -->
<div id="statisticheSection">
  <div class="container mt-4">
    <!-- Sezione per gli istogrammi e selezione anno -->
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <label for="yearSelector" class="form-label me-2">Seleziona anno:</label>
            <select id="yearSelector" class="form-select d-inline-block" style="width: auto;"></select>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Primo istogramma -->
      <div class="col-12 col-md-5">
        <canvas id="myHistogram1" class="chart-canvas"></canvas>
      </div>
      <!-- Secondo istogramma -->
      <div class="col-12 col-md-5 offset-md-2">
        <canvas id="myHistogram2" class="chart-canvas"></canvas>
      </div>
    </div>

    <!-- Quadrati di progresso -->
    <div class="container py-5">
      <div class="custom-flex">
        <!-- Pagine lette oggi -->
        <div class="col-xl-3 col-lg-6 mb-4">
          <div class="bg-white rounded-lg p-5 shadow">
            <h2 class="h6 font-weight-bold text-center mb-4">Pagine lette oggi</h2>
            <div class="progress mx-auto" data-value="<?= $percentualeOggi ?>" data-goal-type="pagineQuotidiane">
              <span class="progress-left">
                <span class="progress-bar border-success"></span>
              </span>
              <span class="progress-right">
                <span class="progress-bar border-success"></span>
              </span>
              <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                <div class="h2 font-weight-bold"><?= $percentualeOggi ?>%</div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal1">Modifica</button>
            </div>
          </div>
        </div>
        <!-- Libri letti nell'ultimo anno -->
        <div class="col-xl-3 col-lg-6 mb-4">
          <div class="bg-white rounded-lg p-5 shadow">
            <h2 class="h6 font-weight-bold text-center mb-4">Libri letti nell'ultimo anno</h2>
            <div class="progress mx-auto" data-value="<?= $percentualeAnnuale ?>" data-goal-type="libriAnnuali">
              <span class="progress-left">
                <span class="progress-bar border-success"></span>
              </span>
              <span class="progress-right">
                <span class="progress-bar border-success"></span>
              </span>
              <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                <div class="h2 font-weight-bold"><?= $percentualeAnnuale ?>%</div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal2">Modifica</button>
            </div>
          </div>
        </div>
        <!-- Lettura libro corrente -->
        <div class="col-xl-3 col-lg-6 mb-4">
          <div class="bg-white rounded-lg p-5 shadow">
            <h2 class="h6 font-weight-bold text-center mb-4">Lettura libro corrente</h2>
            <div class="progress mx-auto" data-value="<?= $percentualeLibro ?>" data-goal-type="pagineLibroCorrente">
              <span class="progress-left">
                <span class="progress-bar border-success"></span>
              </span>
              <span class="progress-right">
                <span class="progress-bar border-success"></span>
              </span>
              <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                <div class="h2 font-weight-bold"><?= $percentualeLibro ?>%</div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal3">Modifica</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modali per aggiornamento obiettivi -->
    <?php foreach(['pagineQuotidiane', 'libriAnnuali', 'pagineLibroCorrente'] as $index => $type): ?>
    <div class="modal fade" id="editModal<?= $index+1 ?>" data-goal-type="<?= $type ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Modifica <?php 
                echo match($type) {
                  'pagineQuotidiane' => 'Pagine Giornaliere',
                  'libriAnnuali' => 'Libri Annuali',
                  'pagineLibroCorrente' => 'Libro Corrente'
                };
              ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            <div class="d-flex align-items-center justify-content-center">
              <button class="btn btn-outline-secondary decrement">-</button>
              <span class="mx-3 fs-4"><?= $goal[$type] ?></span>
              <button class="btn btn-outline-secondary increment">+</button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary save-goal">Salva</button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    
  </div>
</div>

<!-- Pulsante flottante per aggiornare progressi -->
<button type="button" class="btn btn-primary position-fixed bottom-0 end-0 m-4 shadow rounded-circle" data-bs-toggle="modal" data-bs-target="#progressModal" title="Aggiorna progressi">
  <i class="fas fa-plus"></i>
</button>

<!-- Modale Aggiorna Progressi -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="progressModalLabel">Aggiungi libro e progressi</h5>
        <button type="button" class="btn btn-link" id="searchBookBtn" title="Cerca libro per ISBN">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <div class="modal-body">
        <!-- Sezione ricerca ISBN -->
        <div class="mb-3">
          <label for="isbnInput" class="form-label">Ricerca libro per ISBN</label>
          <div class="input-group">
            <input type="text" class="form-control" id="isbnInput" placeholder="Inserisci codice ISBN">
            <button class="btn btn-outline-secondary" type="button" id="searchIsbnBtn">
              Cerca
            </button>
          </div>
          <div id="isbnFeedback" class="form-text"></div>
        </div>

        <!-- Dettagli libro (popolati automaticamente) -->
        <div id="bookDetails" class="mb-4 d-none">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title"><span id="foundTitle"></span></h6>
              <p class="card-text mb-1">Autore: <span id="foundAuthor"></span></p>
              <p class="card-text mb-1">Pagine: <span id="foundPages"></span></p>
              <p class="card-text mb-1">Editore: <span id="foundPublisher"></span></p>
              <button id="useBookDetails" class="btn btn-sm btn-success mt-2">Usa questi dati</button>
            </div>
          </div>
        </div>

        <!-- Form per inserimento manuale libro -->
        <form id="bookForm">
          <h5 class="mb-3">Dettagli libro</h5>
          
          <div class="mb-3">
            <label for="titolo" class="form-label">Titolo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="titolo" name="titolo" required>
          </div>
          
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label for="autore" class="form-label">Autore</label>
              <input type="text" class="form-control" id="autore" name="autore">
            </div>
            <div class="col-md-6 mb-3">
              <label for="genere" class="form-label">Genere</label>
              <input type="text" class="form-control" id="genere" name="genere">
            </div>
          </div>
          
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label for="editore" class="form-label">Editore</label>
              <input type="text" class="form-control" id="editore" name="editore">
            </div>
            <div class="col-md-6 mb-3">
              <label for="numeroPagine" class="form-label">Numero pagine</label>
              <input type="number" class="form-control" id="numeroPagine" name="numeroPagine" min="1">
            </div>
          </div>
          
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label for="dataPubblicazione" class="form-label">Data pubblicazione</label>
              <input type="date" class="form-control" id="dataPubblicazione" name="dataPubblicazione">
            </div>
            <div class="col-md-6 mb-3">
              <label for="dataLettura" class="form-label">Data fine lettura</label>
              <input type="date" class="form-control" id="dataLettura" name="dataLettura">
            </div>
          </div>
        </form>

        <!-- Sezione aggiornamento statistiche -->
        <form id="progressForm" class="mt-4">
          <h5 class="mb-3">Aggiornamento progressi</h5>
          
          <div class="mb-3">
            <label for="pagine-lette-oggi" class="form-label">Pagine lette oggi</label>
            <input type="number" class="form-control" id="pagine-lette-oggi" name="pagine-lette-oggi" min="0" required>
          </div>
          
          <div class="mb-3">
            <label for="pagine-libro-corrente" class="form-label">Pagine totali libro corrente</label>
            <input type="number" class="form-control" id="pagine-libro-corrente" name="pagine-libro-corrente" min="0" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" id="saveProgress">Salva tutto</button>
      </div>
    </div>
  </div>
</div>

<!-- Script per aggiornamento progress bar automatico -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateProgress = async () => {
        try {
            const response = await fetch('../public/checkProgress.php');
            const data = await response.json();
            document.querySelectorAll('.progress').forEach(bar => {
                const type = bar.dataset.goalType;
                const value = Math.round(data[type]);
                bar.dataset.value = value;
                bar.querySelector('.h2').textContent = `${value}%`;
            });
            if (typeof applyProgressBars === 'function') {
                applyProgressBars();
            }
        } catch (error) {
            console.error('Errore aggiornamento:', error);
        }
    };
    setInterval(updateProgress, 30000);
    updateProgress();
});
</script>
</body>
</html>
<?php $conn->close(); ?>