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