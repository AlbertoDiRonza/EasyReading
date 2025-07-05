document.addEventListener("DOMContentLoaded", function () {
  const isbnInput = document.getElementById("isbnInput");
  const bookDetailsDiv = document.getElementById("bookDetails");
  const searchBookBtn = document.getElementById("searchBookBtn");

  // Mostra SEMPRE l'input ISBN
  isbnInput.style.display = "block";

  function searchBookByISBN(isbn) {
    fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`)
      .then(response => response.json())
      .then(data => {
        const bookData = data[`ISBN:${isbn}`];
        if (bookData) {
          displayBookDetails(bookData);
          populateForm(bookData);
        } else {
          bookDetailsDiv.innerHTML = "<p class='text-danger'>Libro non trovato. Compila i dati manualmente.</p>";
        }
      })
      .catch(error => {
        console.error("Errore durante la ricerca del libro:", error);
        bookDetailsDiv.innerHTML = "<p class='text-danger'>Errore nella ricerca. Compila i dati manualmente.</p>";
      });
  }

  function displayBookDetails(bookData) {
    bookDetailsDiv.innerHTML = `
      <h4>${bookData.title}</h4>
      <p><strong>Autore:</strong> ${bookData.authors?.map(a => a.name).join(", ") || "N/A"}</p>
      <p><strong>Editore:</strong> ${bookData.publishers?.map(p => p.name).join(", ") || "N/A"}</p>
      <p><strong>Data di pubblicazione:</strong> ${bookData.publish_date || "N/A"}</p>
      <p><strong>Numero di pagine:</strong> ${bookData.number_of_pages || "N/A"}</p>
    `;
  }

  function populateForm(bookData) {
    document.getElementById("titolo").value = bookData.title || "";
    document.getElementById("autore").value = bookData.authors?.map(a => a.name).join(", ") || "";
    document.getElementById("editore").value = bookData.publishers?.map(p => p.name).join(", ") || "";
    document.getElementById("numeroPagine").value = bookData.number_of_pages || "";
    document.getElementById("dataPubblicazione").value = convertToDateInputFormat(bookData.publish_date);
  }

  function convertToDateInputFormat(dateStr) {
    // Converte 'June 1995' o '1995' in formato '1995-06-01' (approssimato)
    if (!dateStr) return "";
    const date = new Date(dateStr);
    if (!isNaN(date.getTime())) {
      return date.toISOString().split("T")[0];
    }
    return "";
  }

  function handleSearch() {
    const isbn = isbnInput.value.trim();
    if (isbn) {
      searchBookByISBN(isbn);
    } else {
      bookDetailsDiv.innerHTML = "<p class='text-danger'>Inserisci un ISBN valido.</p>";
    }
  }

  searchBookBtn.addEventListener("click", handleSearch);
  isbnInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      handleSearch();
    }
  });
});
