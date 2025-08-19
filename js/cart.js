// js/cart.js
(() => {
  const API = "backend/";
  const tbody = document.getElementById('cartTableBody');
  const totalEl = document.getElementById('cartTotal');
  const checkoutBtn = document.getElementById('checkoutBtn');

  function esc(s){ return String(s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }
  function imgUrl(src){ return src ? src : 'images/placeholder.webp'; }

  function getCart(){ return JSON.parse(localStorage.getItem('cart')||'[]'); }
  function setCart(c){ localStorage.setItem('cart', JSON.stringify(c)); }

  function paint(){
    const cart = getCart();
    if (!tbody) return;
    if (!cart.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="muted">Your cart is empty.</td></tr>';
      totalEl.textContent = '₹0.00';
      return;
    }
    tbody.innerHTML = cart.map((it, i) => `
      <tr>
        <td><img src="${imgUrl(it.image)}" width="64" height="64" style="object-fit:cover;border-radius:6px"></td>
        <td>${esc(it.name)}</td>
        <td>₹${Number(it.price).toFixed(2)}</td>
        <td>
          <button onclick="dec(${i})">-</button>
          ${it.qty}
          <button onclick="inc(${i})">+</button>
        </td>
        <td>₹${(it.price*it.qty).toFixed(2)}</td>
        <td><button onclick="rm(${i})">✕</button></td>
      </tr>`).join('');
    const total = cart.reduce((s,it)=> s + it.price*it.qty, 0);
    totalEl.textContent = '₹' + total.toFixed(2);
  }

  window.inc = function(i){ const c = getCart(); c[i].qty++; setCart(c); paint(); }
  window.dec = function(i){ const c = getCart(); c[i].qty = Math.max(1, c[i].qty-1); setCart(c); paint(); }
  window.rm = function(i){ const c = getCart(); c.splice(i,1); setCart(c); paint(); }

  async function checkWhoami(){
    try {
      const r = await fetch(API + 'whoami.php'); const j = await r.json();
      return (j.ok && j.user) ? j.user : null;
    } catch (e) { return null; }
  }

  async function checkout(){
    const cart = getCart();
    if (!cart.length) return alert('Cart is empty');
    const user = await checkWhoami();
    if (!user) {
      // try to open login modal if exists
      const loginModal = document.getElementById('authModal');
      if (loginModal) { loginModal.classList.add('show'); return; }
      return alert('Please log in to checkout');
    }

    const payload = { items: cart.map(it => ({ id: it.id, qty: it.qty })) };
    try {
      const r = await fetch(API + 'place_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      });
      const j = await r.json();
      if (j.ok) {
        alert(`Order #${j.order_id} placed! Total ₹${Number(j.total).toFixed(2)}`);
        localStorage.removeItem('cart');
        paint();
        // optional: redirect to profile or orders page
        window.location.href = 'profile.html';
      } else {
        alert(j.error || 'Checkout failed');
      }
    } catch (err) {
      console.error('checkout err', err);
      alert('Checkout failed (network)');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (checkoutBtn) checkoutBtn.addEventListener('click', checkout);
    paint();
  });
})();
