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
<script src="js/app.js"></script>
<script>
(async function(){
  const who = await (await fetch('backend/whoami.php')).json();
  const u = who.user;
  if (!u) { alert('Please log in'); return; }
  document.getElementById('profName').textContent = u.name;
  document.getElementById('profEmail').textContent = u.email;
  document.getElementById('profRole').textContent = u.role;

  const r = await (await fetch('backend/get_orders.php')).json();
  if (r.ok) {
    const wrap = document.getElementById('ordersWrap');
    if (!r.orders.length) {
      wrap.innerHTML = '<div class="card">No orders yet.</div>';
    } else {
      wrap.innerHTML = r.orders.map(o=>`
        <div class="card">
          <strong>Order #${o.id}</strong> · <span class="badge">${o.status}</span><br/>
          <small class="muted">${o.created_at}</small>
          <div class="mt-12">Total: <b>₹${Number(o.total).toFixed(2)}</b></div>
          <div class="mt-12">${o.items.map(i=>`${i.name} × ${i.quantity}`).join(', ')}</div>
        </div>`).join('');
    }
  }
})();
</script>
</body>
</html>
