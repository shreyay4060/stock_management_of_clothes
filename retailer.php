<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Retailer — Clothes Stock</title>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<?php include "header.php"; ?>

<main class="container">
  <h1 class="section-title">Retailer Catalogue</h1>
  <p id="retailerMsg" class="msg"></p>

  <section class="grid-cards" id="retailerGrid">
    <!-- Items will load dynamically -->
  </section>
</main>

<?php include "footer.php"; ?>
<script src="js/retailer.js"></script>
</body>
</html>
