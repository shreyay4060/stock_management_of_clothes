<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero compact">
  <h1>Your Cart</h1>
</section>

<section class="container">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr><th>Image</th><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
      </thead>
      <tbody id="cartTableBody"></tbody>
    </table>
  </div>
  <div class="toolbar" style="justify-content:flex-end; gap:12px">
    <strong id="cartTotal">₹0.00</strong>
    <button id="checkoutBtn" class="btn-primary">Checkout</button>
  </div>
</section>

<?php include "footer.php"; ?>
<!-- <script src="js/app.js"></script> -->
<script src="js/cart.js"></script>
</body>
</html>
