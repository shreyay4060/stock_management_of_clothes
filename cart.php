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

<!-- ✅ Address Modal -->
<div id="addressModal" class="modal hidden">
  <div class="modal-content card">
    <h3>Delivery Address</h3>
    <form id="addressForm">
      <input type="text" name="name" placeholder="Full Name" required><br>
      <input type="text" name="phone" placeholder="Phone Number" required><br>
      <input type="text" name="address_line1" placeholder="Address Line 1" required><br>
      <input type="text" name="address_line2" placeholder="Address Line 2"><br>
      <input type="text" name="city" placeholder="City" required><br>
      <input type="text" name="state" placeholder="State" required><br>
      <input type="text" name="pincode" placeholder="Pincode" required><br>
      <p><b>Payment Method:</b> Cash on Delivery only</p>
      <button type="submit" class="btn-primary">Save & Place Order</button>
      <button type="button" onclick="closeAddressModal()">Cancel</button>
    </form>
  </div>
</div>

<?php include "footer.php"; ?>
<script src="js/cart.js"></script>
</body>
</html>
