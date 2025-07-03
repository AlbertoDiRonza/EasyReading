document.addEventListener("DOMContentLoaded", function () {
    const searchBookBtn = document.getElementById("searchBookBtn");
    const isbnInput = document.getElementById("isbnInput");
    const bookDetailsDiv = document.getElementById("bookDetails");

    // Nasconde il campo di input all'apertura della modale
    isbnInput.style.display = "none";

    function searchBookByISBN(isbn) {
      fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`)
        .then(response => response.json())
        .then(data => {
          const bookData = data[`ISBN:${isbn}`];
          if (bookData) {
            displayBookDetails(bookData);
          } else {
            bookDetailsDiv.innerHTML = "<p class='text-danger'>Libro non trovato.</p>";
          }
        })
        .catch(error => {
          console.error("Errore durante la ricerca del libro:", error);
          bookDetailsDiv.innerHTML = "<p class='text-danger'>Errore durante la ricerca. Riprova più tardi.</p>";
        });
    }

    function displayBookDetails(bookData) {
      bookDetailsDiv.innerHTML = `
        <h4>${bookData.title}</h4>
        <p><strong>Autore:</strong> ${bookData.authors ? bookData.authors.map(author => author.name).join(", ") : "N/A"}</p>
        <p><strong>Editore:</strong> ${bookData.publishers ? bookData.publishers.map(publisher => publisher.name).join(", ") : "N/A"}</p>
        <p><strong>Data di pubblicazione:</strong> ${bookData.publish_date || "N/A"}</p>
        <p><strong>Numero di pagine:</strong> ${bookData.number_of_pages || "N/A"}</p>
      `;
    }

    function handleSearch() {
      const isbn = isbnInput.value.trim();
      if (isbn) {
        searchBookByISBN(isbn);
      } else {
        bookDetailsDiv.innerHTML = "<p class='text-danger'>Inserisci un ISBN valido.</p>";
      }
    }

    // Mostra il campo ISBN quando si preme la lente
    searchBookBtn.addEventListener("click", function () {
      if (isbnInput.style.display === "none") {
        isbnInput.style.display = "block";
        isbnInput.focus(); // Mette automaticamente a fuoco il campo
      } else {
        handleSearch();
      }
    });

    // Avvia la ricerca quando si preme Enter
    isbnInput.addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        handleSearch();
      }
    });
  });