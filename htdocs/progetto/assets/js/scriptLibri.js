document.addEventListener('DOMContentLoaded', () => {
    // Controllo esistenza elementi
    const libriContainer = document.getElementById('libriContainer');
    const loading = document.getElementById('loading');
    const emptyState = document.getElementById('emptyState');
    const libriCount = document.getElementById('libriCount');
    
    if (!libriContainer || !loading || !emptyState || !libriCount) return;

    async function fetchLibri() {
        try {
            // Usiamo userData dall'oggetto globale
            const response = await fetch(`../public/getLibri.php?idUtente=${idUtente}`);
            
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            
            const libri = await response.json();
            
            if (libri.length > 0) {
                renderLibri(libri);
                libriCount.textContent = libri.length;
                libriContainer.classList.remove('d-none');
                emptyState.classList.add('d-none');
            } else {
                showEmptyState();
            }
        } catch (error) {
            console.error('Errore nel recupero dei libri:', error);
            alert('Errore nel caricamento dei libri');
        } finally {
            loading.classList.add('d-none');
        }
    }
  function renderLibri(libri) {
      libriContainer.innerHTML = libri.map(libro => `
          <div class="col-12 col-md-6 col-lg-4">
              <div class="card book-card mb-3 shadow-sm">
                  <div class="card-body">
                      <div class="d-flex justify-content-between">
                          <div>
                              <h5 class="card-title">${libro.titolo}</h5>
                              <p class="card-text text-muted">${libro.autore}</p>
                          </div>
                          <button class="btn btn-danger delete-btn align-self-start" 
                                  onclick="deleteLibro(${libro.id}, this)">
                              <i class="fas fa-trash"></i>
                          </button>
                      </div>
                      <div class="mt-2">
                          <small class="text-muted">
                              <i class="fas fa-calendar-alt me-1"></i>
                              Letto il: ${new Date(libro.dataLettura).toLocaleDateString('it-IT')}
                          </small>
                      </div>
                  </div>
              </div>
          </div>
      `).join('');
  }

  function showEmptyState() {
      emptyState.classList.remove('d-none');
      libriContainer.classList.add('d-none');
  }

  window.deleteLibro = async (idLibro, button) => {
      if (!confirm('Sei sicuro di voler eliminare questo libro?')) return;

      button.disabled = true;
      button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      try {
          const response = await fetch(`../public/rimuoviLibro.php?id=${idLibro}`, {
              method: 'DELETE'
          });

          const result = await response.json();
          
          if (result.success) {
              button.closest('.col-12').remove();
              libriCount.textContent = parseInt(libriCount.textContent) - 1;
              
              if (parseInt(libriCount.textContent) === 0) {
                  showEmptyState();
              }
          } else {
              throw new Error(result.message);
          }
      } catch (error) {
          console.error('Errore eliminazione libro:', error);
          alert(error.message);
      } finally {
          button.disabled = false;
          button.innerHTML = '<i class="fas fa-trash"></i>';
      }
  };

  // Inizializzazione
  fetchLibri();
});