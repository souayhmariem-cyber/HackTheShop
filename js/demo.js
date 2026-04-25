function addComment() {
  const author = document.getElementById('commentAuthor').value;
  const text = document.getElementById('commentText').value;

  if (author === '' || text === '') {
    alert('Please fill in all fields!');
    return;
  }

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
  const query = document.getElementById('searchInput2').value;
  const result = document.getElementById('searchResult');

  if (query === '') {
    result.innerHTML = '<p class="no-result">Type something to search.</p>';
    return;
  }

  const products = [
    { name: 'Laptop Pro X', price: '$799', img: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80' },
    { name: 'Gaming Laptop Z', price: '$1299', img: 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80' },
    { name: 'Smartphone Z12', price: '$499', img: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80' },
    { name: 'iPhone 15 Pro', price: '$1099', img: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80' },
    { name: 'AirPods Pro 2', price: '$249', img: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80' },
    { name: 'Sony WH-1000XM5', price: '$349', img: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80' },
    { name: 'Apple Watch S9', price: '$399', img: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80' },
    { name: 'PlayStation 5', price: '$499', img: 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80' },
    { name: 'Dell 27" 4K Monitor', price: '$599', img: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80' },
    { name: 'Logitech MX Master', price: '$99', img: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80' },
  ];

  const filtered = products.filter(p =>
    p.name.toLowerCase().includes(query.toLowerCase())
  );

  if (filtered.length === 0) {
    result.innerHTML = `<p class="no-result">No products found for "${query}"</p>`;
    return;
  }

  result.innerHTML = `<p class="search-info">Results for : <strong style="color:#a78bfa;">${query}</strong></p>`;

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
            <button class="btn-cart" onclick="addToCart('${p.name}')">
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
  if (q) {
    document.getElementById('searchInput2').value = q;
    doSearch();
  }
}