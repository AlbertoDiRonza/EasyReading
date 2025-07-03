document.addEventListener("DOMContentLoaded", function () {
  // Seleziona tutte le modali degli obiettivi
  document.querySelectorAll(".modal").forEach(modal => {
    // Al momento della visualizzazione della modale...
    modal.addEventListener("show.bs.modal", function () {
      // Recupera l'elemento che mostra il valore corrente (il <span class="fs-4">)
      const countDisplay = modal.querySelector(".fs-4");
      // Ottieni il tipo di obiettivo dalla data attribute
      const goalType = modal.getAttribute("data-goal-type");
      // Leggi il valore corrente (lo converto in numero, con fallback a 0)
      const currentValue = parseInt(countDisplay.textContent) || 0;
      // Inizializza una variabile di appoggio per il conteggio
      let count = currentValue;
      
      // Seleziona i pulsanti presenti nella modale
      const btnIncrement = modal.querySelector(".increment");
      const btnDecrement = modal.querySelector(".decrement");
      const btnSave = modal.querySelector(".save-goal");

      // Associa il click per incrementare il conteggio
      btnIncrement.onclick = function () {
        count++;
        countDisplay.textContent = count;
      };

      // Associa il click per decrementare, impedendo di scendere sotto 1
      btnDecrement.onclick = function () {
        if (count > 1) {
          count--;
          countDisplay.textContent = count;
        }
      };

      // Al click sul pulsante salva, aggiorna l'obiettivo sul server
      btnSave.onclick = function () {
        updateGoal(goalType, count)
          .then(response => {
            if (response.success) {
              console.log("Obiettivo aggiornato con successo!");
              // Chiudi la modale usando l'istanza di Bootstrap
              let modalInstance = bootstrap.Modal.getInstance(modal);
              modalInstance.hide();
            } else {
              console.error("Errore: " + response.message);
              alert("Errore durante l'aggiornamento: " + response.message);
            }
          })
          .catch(error => {
            console.error("Errore di rete: ", error);
            alert("Errore di rete. Riprova più tardi.");
          });
      };
    });
  });
});

// Funzione per inviare la richiesta di aggiornamento dell'obiettivo
function updateGoal(goalType, newGoalValue) {
  return fetch('../public/updateGoals.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `goalType=${encodeURIComponent(goalType)}&newValue=${encodeURIComponent(newGoalValue)}`
  })
  .then(response => {
      if (!response.ok) { // Controlla lo status HTTP
          return response.text().then(text => {
              throw new Error(`HTTP ${response.status}: ${text}`);
          });
      }
      return response.json();
  })
  .catch(error => {
      console.error('Fetch error:', error);
      throw error; // Rilancia per gestirlo nel chiamante
  })
  .then(data => {
    if (data.success) {
      // Se l'aggiornamento ha avuto successo, aggiorna anche la barra di progresso
      fetch(`../public/getProgress.php?goalType=${goalType}`)
        .then(response => response.json())
        .then(progressData => {
          const currentProgress = progressData.currentProgress;
          const percentage = Math.round((currentProgress / newGoalValue) * 100);
          
          // Seleziona la barra di progresso corrispondente
          const progressBar = document.querySelector(`.progress[data-goal-type="${goalType}"]`);
          if (progressBar) {
            progressBar.setAttribute('data-value', percentage);
            const valueDiv = progressBar.querySelector('.progress-value .h2');
            if (valueDiv) {
              valueDiv.textContent = `${percentage}%`;
            }
            // Richiama la funzione che applica lo stile alle progress bar (già presente nel tuo progetto)
            if (typeof applyProgressBars === 'function') {
              applyProgressBars();
            }
          }
        });
    }
    return data;
  });
}
