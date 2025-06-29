function createPost() {
    const text = document.getElementById('postText').value;
    if (!text.trim()) return;
    
    const postDiv = document.createElement('div');
    postDiv.className = 'post';
    postDiv.innerHTML = `
    <p><strong>You</strong></p>
    <p>${text}</p>
    <div class="post-actions">
      <span>👍 Like</span>
      <span>💬 Comment</span>
      <span>🔁 Share</span>
      </div>
      `;
      
      document.getElementById('posts').prepend(postDiv);
      document.getElementById('postText').value = '';
}