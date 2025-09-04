<?php
require "db.php";

$name = trim($_POST['name'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$size = trim($_POST['size'] ?? '');
$color = trim($_POST['color'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$image = null;

// ✅ Ensure uploads directory exists
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ✅ Handle image upload securely
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $mime = mime_content_type($_FILES['image']['tmp_name']);

    if (in_array($mime, $allowed)) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($_FILES['image']['name']));
        $target = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $filename;
        }
    }
}

$stmt = $pdo->prepare("INSERT INTO clothes (name, brand, size, color, price, quantity, image) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$name, $brand, $size, $color, $price, $quantity, $image])) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["ok" => false, "error" => "DB insert failed"]);
}
