<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Delivery Address — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero">
  <h1>Delivery Address</h1>
  <p>Please enter your delivery address to place the order.</p>
</section>

<section class="container">
  <div class="card" style="max-width:500px;margin:auto;">
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

      <button type="submit" class="btn btn-primary">Save & Place Order</button>
      <button type="button" id="cancelBtn">Cancel</button>
    </form>
  </div>
</section>

<?php include "footer.php"; ?>

<script>
document.getElementById("addressForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const form = e.target;
  const data = Object.fromEntries(new FormData(form).entries());

  try {
    // ✅ Save address
    const res = await fetch("backend/save_address.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
      credentials: "include"
    });
    const out = await res.json();

    if (!out.ok) {
      alert("Failed to save address: " + out.error);
      return;
    }

    const address_id = out.address_id;

    // ✅ Fetch cart from localStorage
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (!cart.length) {
      alert("Cart is empty!");
      window.location.href = "cart.php";
      return;
    }

    // ✅ Place order immediately
    const orderRes = await fetch("backend/place_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        items: cart.map(it => ({ id: it.id, qty: it.qty })),
        address_id: address_id
      }),
      credentials: "include"
    });

    const orderOut = await orderRes.json();
    if (orderOut.ok) {
      alert(`Order #${orderOut.order_id} placed successfully! Total ₹${Number(orderOut.total).toFixed(2)}\nPayment Method: Cash on Delivery`);
      localStorage.removeItem("cart");
      window.location.href = "profile.php";
    } else {
      alert("Order failed: " + (orderOut.error || "Unknown error"));
    }
  } catch (err) {
    console.error("Address/order error", err);
    alert("Network error while placing order.");
  }
});

// ✅ Cancel button
document.getElementById("cancelBtn").addEventListener("click", () => {
  window.location.href = "cart.php";
});
</script>
</body>
</html>
