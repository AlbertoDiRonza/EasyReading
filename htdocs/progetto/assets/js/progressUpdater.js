document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById('progressForm');
  const modalEl = document.getElementById('progressModal');
  const saveBtn = document.getElementById("saveProgress");

  saveBtn.addEventListener("click", async () => {
      try {
          // Creazione del FormData dal form
          const formData = new FormData(form);
          // Invia i dati al file PHP per aggiornare le statistiche
          const response = await fetch('../public/updateStatistiche.php', {
              method: 'POST',
              body: formData
          });
          const result = await response.json();
          if (result.status !== 'success') {
              throw new Error(result.message || 'Errore nel salvataggio');
          }
          // Aggiorna le progress bar
          await updateStatisticheUI();
          // Chiude la modale
          const modalInstance = bootstrap.Modal.getInstance(modalEl);
          modalInstance.hide();
          // Resetta il form
          form.reset();
          alert("Statistiche e grafici aggiornati correttamente!");
      } catch (error) {
          console.error('Errore nel salvataggio:', error);
          alert("Errore: " + error.message);
      }
  });
});

// Aggiorna le progress bar richiamando checkProgress.php
async function updateStatisticheUI() {
  try {
      const response = await fetch('../public/checkProgress.php');
      const data = await response.json();
      document.querySelectorAll('.progress').forEach(bar => {
          const type = bar.dataset.goalType;
          const value = Math.round(data[type]);
          bar.dataset.value = value;
          bar.querySelector('.h2').textContent = `${value}%`;
          const left = bar.querySelector('.progress-left .progress-bar');
          const right = bar.querySelector('.progress-right .progress-bar');
          const deg = value * 3.6;
          right.style.transform = `rotate(${Math.min(deg, 180)}deg)`;
          left.style.transform = `rotate(${Math.max(deg - 180, 0)}deg)`;
      });
  } catch (error) {
      console.error('Errore aggiornamento UI statistiche:', error);
  }
}