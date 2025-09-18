<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero">
  <h1>Your Profile</h1>
  <p>Role-based dashboard and order history.</p>
</section>

<section class="container grid-2 align-start">
  <div class="card">
    <h3>Profile</h3>
    <p id="profName">—</p>
    <p id="profEmail" class="muted">—</p>
    <p><span class="badge" id="profRole">retailer</span></p>
  </div>

  <div class="card">
    <h3>Order History</h3>
    <div id="ordersWrap" class="grid-cards"></div>
  </div>
</section>

<?php include "footer.php"; ?>

<script>
function escapeHtml(s) {
  return String(s || "").replace(/[&<>"']/g, (m) => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"
  }[m]));
}

(async function(){
  // ✅ Get logged-in user
  const who = await (await fetch('backend/whoami.php', { credentials: "include" })).json();
  const u = who.user;
  if (!u) { 
    alert('Please log in'); 
    window.location.href = "index.php"; 
    return; 
  }
  document.getElementById('profName').textContent = u.name;
  document.getElementById('profEmail').textContent = u.email;
  document.getElementById('profRole').textContent = u.role;

  // ✅ Fetch orders
  const r = await (await fetch('backend/get_orders.php', { credentials: "include" })).json();
  if (r.ok) {
    const wrap = document.getElementById('ordersWrap');
    if (!r.orders.length) {
      wrap.innerHTML = '<div class="card">No orders yet.</div>';
    } else {
      wrap.innerHTML = r.orders.map(o=>`
        <div class="card">
          <strong>Order #${o.id}</strong> · 
          <span class="badge">${escapeHtml(o.status)}</span><br/>
          <small class="muted">${escapeHtml(o.created_at)}</small>

          <div class="mt-12">Total: <b>₹${Number(o.total).toFixed(2)}</b></div>

          <div class="mt-12">
            ${o.items.map(i=>`
              <div style="display:flex;align-items:center;gap:8px;">
                <img src="${i.image ? escapeHtml(i.image) : 'images/arrival3.jpg'}" 
                     style="width:40px;height:40px;object-fit:cover;border-radius:4px;"
                     alt="${escapeHtml(i.name)}">
                <span>${escapeHtml(i.name)} × ${i.quantity} — ₹${(i.price * i.quantity).toFixed(2)}</span>
              </div>
            `).join('')}
          </div>

          ${o.address ? `
            <div class="mt-12 muted" style="font-size:0.9em;">
              <b>Delivery Address:</b><br/>
              ${escapeHtml(o.address.name)} (${escapeHtml(o.address.phone)})<br/>
              ${escapeHtml(o.address.address_line1)} ${escapeHtml(o.address.address_line2 || '')}<br/>
              ${escapeHtml(o.address.city)}, ${escapeHtml(o.address.state)} - ${escapeHtml(o.address.pincode)}
            </div>
          ` : ""}
        </div>
      `).join('');
    }
  }
})();
</script>
</body>
</html>
