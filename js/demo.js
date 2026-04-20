function addComment() {
  const author = document.getElementById('commentAuthor').value;
  const text = document.getElementById('commentText').value;

  if (author === '' || text === '') {
    alert('Remplis tous les champs !');
    return;
  }

  // VOLONTAIREMENT VULNÉRABLE — innerHTML permet le XSS
  const commentsList = document.getElementById('commentsList');
  commentsList.innerHTML += `
    <div class="comment-card">
      <div class="comment-author">${author}</div>
      <div class="comment-text">${text}</div>
    </div>
  `;

  document.getElementById('commentAuthor').value = '';
  document.getElementById('commentText').value = '';
}

function doSearch() {
  const query = document.getElementById('searchInput').value;
  const result = document.getElementById('searchResult');

  if (query === '') {
    result.innerHTML = '<p class="no-result">Tape quelque chose pour rechercher.</p>';
    return;
  }

  // VOLONTAIREMENT VULNÉRABLE — innerHTML permet le XSS
  result.innerHTML = `
    <p class="search-info">Résultats pour : <strong>${query}</strong></p>
    <div class="grid">
      <div class="card">
        <div class="card-img">💻</div>
        <div class="card-body">
          <div class="card-title">Laptop Pro X</div>
          <div><span class="card-price">799€</span></div>
          <button class="add-btn">Ajouter au panier</button>
        </div>
      </div>
    </div>
  `;
}

// XSS via URL
window.onload = function() {
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  if (q) {
    document.getElementById('searchInput').value = q;
    doSearch();
  }
}