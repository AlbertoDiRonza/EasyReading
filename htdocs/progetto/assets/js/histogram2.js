document.addEventListener("DOMContentLoaded", function() {
  var ctx2 = document.getElementById('myHistogram2').getContext('2d');
  var myHistogram2 = new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: 'Generi preferiti',
        data: [],
        backgroundColor: 'rgba(220, 234, 178, 0.66)',
        borderColor: 'rgb(0, 0, 0)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true } }
    }
  });

  function loadGenres() {
    fetch('../public/getGenres.php')
      .then(response => response.json())
      .then(data => {
         if (data.labels && data.data) {
           const ctx2 = document.getElementById('myHistogram2').getContext('2d');
           const chart = Chart.getChart(ctx2);
           chart.data.labels = data.labels;
           chart.data.datasets[0].data = data.data;
           chart.update();
         } else {
           console.error("Errore nei dati dei generi:", data.error);
         }
      })
      .catch(error => console.error("Errore durante il fetch dei generi:", error));
  }
  loadGenres();
});
