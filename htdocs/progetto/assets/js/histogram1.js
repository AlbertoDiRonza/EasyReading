document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById('myHistogram1').getContext('2d');
  const currentYear = new Date().getFullYear();

  // Crea il grafico con dati iniziali a zero
  const myHistogram = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [
        'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 
        'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
      ],
      datasets: [{
        label: `Libri letti nell'anno ${currentYear}`,
        data: new Array(12).fill(0),
        backgroundColor: 'rgba(179, 214, 198, 0.66)',
        borderColor: 'rgb(0, 0, 0)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0 }
        }
      }
    }
  });

  // Funzione per caricare i dati mensili dei libri
  function loadMonthlyBooks(year) {
    fetch(`../public/getMonthlyBooks.php?year=${year}`)
      .then(response => response.json())
      .then(data => {
        if (Array.isArray(data) && data.length === 12) {
          const ctx1 = document.getElementById('myHistogram1').getContext('2d');
          const chart = Chart.getChart(ctx1);
          chart.data.datasets[0].data = data;
          chart.data.datasets[0].label = `Libri letti nell'anno ${year}`;
          chart.update();
        } else {
          console.error("Dati non validi ricevuti per i libri mensili:", data);
        }
      })
      .catch(error => console.error("Errore durante il fetch dei libri mensili:", error));
  }

  // Carica i dati iniziali per l'anno corrente
  loadMonthlyBooks(currentYear);

  // Se esiste il selettore degli anni, lo popola e gestisce il cambio anno
  const yearSelector = document.getElementById("yearSelector");
  if (yearSelector) {
    for (let y = currentYear; y >= currentYear - 10; y--) {
      const option = document.createElement("option");
      option.value = y;
      option.textContent = y;
      if (y === currentYear) {
        option.selected = true;
      }
      yearSelector.appendChild(option);
    }
    yearSelector.addEventListener("change", function() {
      loadMonthlyBooks(parseInt(this.value));
    });
  }
});
