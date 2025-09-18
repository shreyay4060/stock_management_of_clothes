<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
  <style>
    /* Modal styling */
    .modal {
      position: fixed; top:0; left:0; width:100%; height:100%;
      background: rgba(0,0,0,0.6);
      display: flex; align-items: center; justify-content: center;
      z-index: 1000;
    }
    .modal.hidden { display: none; }
    .modal-content {
      background: #fff; padding: 20px; border-radius: 8px;
      width: 100%; max-width: 480px;
    }
    .modal-content h3 {
      margin-top: 0; margin-bottom: 15px; text-align: center;
    }
    .modal-content form label {
      display: block; margin-top: 10px; font-weight: bold;
    }
    .modal-content form input {
      width: 100%; padding: 8px; margin-top: 4px;
      border: 1px solid #ccc; border-radius: 4px;
    }
    .modal-content .btn-row {
      margin-top: 15px; display: flex; justify-content: space-between;
      gap: 10px;
    }
  </style>
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
      <label>Full Name</label>
      <input type="text" name="name" required>

      <label>Phone Number</label>
      <input type="text" name="phone" required>

      <label>Address Line 1</label>
      <input type="text" name="address_line1" required>

      <label>Address Line 2</label>
      <input type="text" name="address_line2">

      <label>City</label>
      <input type="text" name="city" required>

      <label>State</label>
      <input type="text" name="state" required>

      <label>Pincode</label>
      <input type="text" name="pincode" required>

      <p><b>Payment Method:</b> Cash on Delivery only</p>

      <div class="btn-row">
        <button type="submit" class="btn-primary">Save & Place Order</button>
        <button type="button" onclick="closeAddressModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php include "footer.php"; ?>
<script src="js/cart.js"></script>
</body>
</html>
