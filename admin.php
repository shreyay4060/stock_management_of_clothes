<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<main class="container">
  <h1 class="section-title">Admin Dashboard</h1>
  <p id="adminMsg" class="msg"></p>

  <!-- Add Cloth Form -->
  <section class="card">
    <h2>Add New Cloth</h2>
    <form id="addClothForm" enctype="multipart/form-data" class="form-grid">
      <input type="text" name="name" placeholder="Cloth Name" required>
      <input type="text" name="brand" placeholder="Brand">
      <input type="text" name="size" placeholder="Size">
      <input type="text" name="color" placeholder="Color">
      <input type="number" step="0.01" name="price" placeholder="Price" required>
      <input type="number" name="quantity" placeholder="Quantity" required>
      <input type="file" name="image" accept="image/*">
      <button type="submit" class="btn-primary">Add Cloth</button>
    </form>
  </section>

  <!-- Clothes Table -->
  <section class="card">
    <h2>Available Clothes</h2>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th><th>Name</th><th>Brand</th><th>Size</th><th>Color</th>
            <th>Price</th><th>Qty</th><th>Image</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="clothesTable"></tbody>
      </table>
    </div>
  </section>
</main>

<?php include "footer.php"; ?>
<script src="js/admin.js"></script>
</body>
</html>
