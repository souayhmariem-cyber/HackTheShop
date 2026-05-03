// ── Load Comments ─────────────────────────────────────────────────────────────
function loadComments() {
  fetch('php/comments.php?action=get&product=general')
    .then(r => r.json())
    .then(comments => {
      const list = document.getElementById('commentsList');
      if (!list) return;
      list.innerHTML = '';
      if (comments.length === 0) {
        list.innerHTML = '<p style="color:#534AB7;text-align:center;padding:20px;">No reviews yet.</p>';
        return;
      }
      comments.forEach(c => {
        const div = document.createElement('div');
        div.className = 'comment-card';
        div.innerHTML = `
          <div class="comment-author">${c.author}</div>
          <div class="comment-text">${c.text}</div>
          <div class="comment-date">${new Date(c.created_at).toLocaleDateString()}</div>
        `;
        list.appendChild(div);
      });
    }).catch(() => {});
}

// ── Add Comment ────────────────────────────────────────────────────────────────
function addComment() {
  const author = document.getElementById('commentAuthor')?.value.trim();
  const text   = document.getElementById('commentText')?.value.trim();
  if (!author || !text) { alert('Please fill in all fields!'); return; }

  fetch('php/comments.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body:    `author=${encodeURIComponent(author)}&text=${encodeURIComponent(text)}&product=general`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('commentAuthor').value = '';
      document.getElementById('commentText').value   = '';
      loadComments();
    }
  });
}

window.addEventListener('load', loadComments);

// ── Client-side Search ────────────────────────────────────────────────────────
function doSearch() {
  const query  = document.getElementById('searchInput2')?.value || '';
  const result = document.getElementById('searchResult');
  if (!result) return;

  if (!query.trim()) {
    result.innerHTML = '<p class="no-result">Type something to search.</p>';
    return;
  }

  const products = Object.entries(window.PRODUCTS_DATA || {}).map(([name, p]) => ({ name, ...p }));
  const filtered = products.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));

  if (filtered.length === 0) {
    result.innerHTML = `<p class="no-result">No products found for "${query}"</p>`;
    return;
  }

  result.innerHTML = `<p class="search-info">Results for: <strong style="color:#a78bfa;">${query}</strong> (${filtered.length} results)</p>`;
  const grid = document.createElement('div');
  grid.className = 'products-grid';
  filtered.forEach(p => {
    grid.innerHTML += `
      <div class="product-card">
        <img class="product-img" src="${p.img}" alt="${p.name}">
        <div class="product-info">
          <div class="product-name">${p.name}</div>
          <div class="product-price">${p.price}</div>
          <div class="product-actions">
            <button class="btn-cart" onclick="addToCart('${p.name}','${p.price}')">
              <i class="fa fa-cart-plus"></i> Add
            </button>
          </div>
        </div>
      </div>
    `;
  });
  result.appendChild(grid);
}

window.onload = function() {
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  if (q && document.getElementById('searchInput2')) {
    document.getElementById('searchInput2').value = q;
    doSearch();
  }
};