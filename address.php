<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Address — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero">
  <h1>Your Delivery Address</h1>
  <p>We’ll deliver your order to this address. You can update it anytime.</p>
</section>

<section class="container">
  <div class="card">
    <form id="addressForm" class="form-grid">
      <label>Full Name*
        <input type="text" name="name" id="name" required>
      </label>

      <label>Phone Number*
        <input type="text" name="phone" id="phone" required>
      </label>

      <label>Address Line 1*
        <input type="text" name="address_line1" id="address_line1" required>
      </label>

      <label>Address Line 2
        <input type="text" name="address_line2" id="address_line2">
      </label>

      <label>City*
        <input type="text" name="city" id="city" required>
      </label>

      <label>State*
        <input type="text" name="state" id="state" required>
      </label>

      <label>Pincode*
        <input type="text" name="pincode" id="pincode" required>
      </label>

      <button type="submit" class="btn-primary">Save Address</button>
    </form>
  </div>
</section>

<?php include "footer.php"; ?>

<script>
(async function(){
  try {
    // ✅ Load existing address if available
    const res = await fetch("backend/get_address.php", { credentials: "include" });
    const data = await res.json();
    if (data.ok && data.address) {
      const addr = data.address;
      document.getElementById("name").value = addr.name || "";
      document.getElementById("phone").value = addr.phone || "";
      document.getElementById("address_line1").value = addr.address_line1 || "";
      document.getElementById("address_line2").value = addr.address_line2 || "";
      document.getElementById("city").value = addr.city || "";
      document.getElementById("state").value = addr.state || "";
      document.getElementById("pincode").value = addr.pincode || "";
    }
  } catch (err) {
    console.error("Error loading address:", err);
  }

  // ✅ Save handler
  document.getElementById("addressForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const body = {
      name: document.getElementById("name").value.trim(),
      phone: document.getElementById("phone").value.trim(),
      address_line1: document.getElementById("address_line1").value.trim(),
      address_line2: document.getElementById("address_line2").value.trim(),
      city: document.getElementById("city").value.trim(),
      state: document.getElementById("state").value.trim(),
      pincode: document.getElementById("pincode").value.trim(),
    };

    try {
      const res = await fetch("backend/save_address.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
        credentials: "include"
      });
      const data = await res.json();
      if (data.ok) {
        alert("Address saved successfully!");
        window.location.href = "cart.php"; // 🔑 back to cart for checkout
      } else {
        alert("Failed to save address: " + (data.error || "Unknown error"));
      }
    } catch (err) {
      console.error("Save address error", err);
      alert("Network error while saving address");
    }
  });
})();
</script>
</body>
</html>
