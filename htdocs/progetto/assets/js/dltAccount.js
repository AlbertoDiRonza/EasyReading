document.addEventListener('DOMContentLoaded', function () {
  const deleteAccountButton = document.getElementById('deleteAccount');

  if (deleteAccountButton) {
    deleteAccountButton.addEventListener('click', function () {
      if (confirm("Sei sicuro di voler eliminare il tuo account? Questa azione è irreversibile!")) {
        fetch('../public/deleteAccount.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'include', // Aggiungi questa linea
          body: JSON.stringify({ userId: idUtente })
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              window.location.href = "login.html";
            }
            alert(data.message);
          })
          .catch(error => {
            console.error('Fetch Error:', error);
            alert("Errore durante la comunicazione col server");
          });
      }
    });
  }
});