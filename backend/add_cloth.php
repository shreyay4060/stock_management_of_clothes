<?php
require "db.php";

$name = $_POST['name'] ?? '';
$brand = $_POST['brand'] ?? '';
$size = $_POST['size'] ?? '';
$color = $_POST['color'] ?? '';
$price = $_POST['price'] ?? 0;
$quantity = $_POST['quantity'] ?? 0;
$image = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $filename = time() . "_" . basename($_FILES['image']['name']);
    $target = __DIR__ . "/uploads/" . $filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $image = $filename;
}

$stmt = $pdo->prepare("INSERT INTO clothes (name, brand, size, color, price, quantity, image) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$name, $brand, $size, $color, $price, $quantity, $image])) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["ok" => false, "error" => "DB insert failed"]);
}
