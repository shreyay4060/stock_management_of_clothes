<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Orders — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
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
(async function(){
  const res = await (await fetch('backend/get_all_orders.php')).json();
  const wrap = document.getElementById('ordersWrap');

  if (!res.ok) {
    wrap.innerHTML = `<div class="card">Failed to load orders</div>`;
    return;
  }

  if (!res.orders.length) {
    wrap.innerHTML = `<div class="card">No orders yet.</div>`;
    return;
  }

  wrap.innerHTML = res.orders.map(o => `
    <div class="card">
      <strong>Order #${o.id}</strong> · <span class="badge">${o.status}</span><br/>
      <small class="muted">By: ${o.user_name} (${o.user_email}) · ${o.created_at}</small>
      <div class="mt-12">Total: <b>₹${Number(o.total).toFixed(2)}</b></div>
      <div class="mt-12 grid-cards">
        ${o.items.map(i => `
          <div class="card small">
            <img src="${i.image || 'images/placeholder.webp'}" width="50" height="50" style="object-fit:cover;border-radius:4px;">
            ${i.name} × ${i.quantity} @ ₹${i.price}
          </div>`).join('')}
      </div>
    </div>
  `).join('');
})();
</script>
</body>
</html>
