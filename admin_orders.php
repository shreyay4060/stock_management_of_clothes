<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Orders — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
  <style>
    /* slight card tweaks for order address */
    .order-address { font-size: 0.95rem; margin-top: 8px; color: #444; }
    .order-actions { margin-top: 12px; display:flex; gap:8px; align-items:center; }
    .btn-fulfill { background:#198754; color:white; border:0; padding:8px 12px; border-radius:6px; cursor:pointer; }
    .badge.pending { background:#0d6efd; color:white; padding:4px 8px; border-radius:999px; }
    .badge.fulfilled { background:#198754; color:white; padding:4px 8px; border-radius:999px; }
  </style>
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero">
  <h1>All Orders</h1>
  <p>Manage and view customer orders.</p>
</section>

<section class="container">
  <div id="ordersWrap" class="grid-cards"></div>
</section>

<?php include "footer.php"; ?>

<script>
async function markFulfilled(orderId) {
  if (!confirm("Mark order #" + orderId + " as fulfilled?")) return;
  try {
    const res = await fetch('backend/update_order_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ order_id: orderId, status: 'fulfilled' })
    });
    const data = await res.json();
    if (data.ok) {
      alert('Order marked as fulfilled (email sent if configured).');
      // reload to reflect updated status
      window.location.reload();
    } else {
      alert('Failed: ' + (data.error || 'Unknown error'));
    }
  } catch (err) {
    console.error(err);
    alert('Network error while updating order.');
  }
}

(function(){
  (async function loop() {
    try {
      const r = await (await fetch('backend/get_all_orders.php', { credentials: 'include' })).json();
      const wrap = document.getElementById('ordersWrap');
      if (!r.ok) {
        wrap.innerHTML = '<div class="card">Failed to load orders</div>';
        return;
      }
      if (!r.orders.length) {
        wrap.innerHTML = '<div class="card">No orders yet.</div>';
        return;
      }

      wrap.innerHTML = r.orders.map(o => {
        const badgeClass = (o.status === 'fulfilled') ? 'badge fulfilled' : 'badge pending';
        const addressHtml = o.address ? `
          <div class="order-address">
            <strong>Deliver to:</strong><br/>
            ${escapeHtml(o.address.name || '')} ${o.address.phone ? '(' + escapeHtml(o.address.phone) + ')' : ''}<br/>
            ${escapeHtml(o.address.address_line1 || '')} ${escapeHtml(o.address.address_line2 || '')}<br/>
            ${escapeHtml(o.address.city || '')}, ${escapeHtml(o.address.state || '')} - ${escapeHtml(o.address.pincode || '')}
          </div>` : '<div class="order-address muted">No address provided.</div>';

        const itemsHtml = (o.items || []).map(i => `
          <div class="card small" style="display:flex;gap:10px;align-items:center;padding:8px;">
            <img src="${escapeHtml(i.image || 'images/arrival3.jpg')}" width="50" height="50" style="object-fit:cover;border-radius:6px;">
            <div>${escapeHtml(i.name)} × ${i.quantity} &nbsp; @ ₹${Number(i.price).toFixed(2)}</div>
          </div>
        `).join('');

        return `
          <div class="card">
            <strong>Order #${o.id}</strong> · <span class="${badgeClass}">${escapeHtml(o.status)}</span><br/>
            <small class="muted">By: ${escapeHtml(o.user_name || '—')} (${escapeHtml(o.user_email || '—')}) · ${escapeHtml(o.created_at)}</small>
            <div class="mt-12">Total: <b>₹${Number(o.total).toFixed(2)}</b></div>
            <div class="mt-12 grid-cards">${itemsHtml}</div>
            ${addressHtml}
            <div class="order-actions">
              ${o.status === 'pending' ? `<button class="btn-fulfill" onclick="markFulfilled(${o.id})">Mark as Fulfilled</button>` : ''}
            </div>
          </div>
        `;
      }).join('');
    } catch (err) {
      console.error('Orders load error', err);
      document.getElementById('ordersWrap').innerHTML = '<div class="card">Error loading orders.</div>';
    }
  })();
})();

function escapeHtml(s) {
  return String(s || '').replace(/[&<>"']/g, (m) => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  })[m]);
}
</script>
</body>
</html>
