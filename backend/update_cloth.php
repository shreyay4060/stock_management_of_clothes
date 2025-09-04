<?php
require __DIR__ . "/db.php";
require_admin();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "Invalid id"]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$size = trim($_POST['size'] ?? '');
$color = trim($_POST['color'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

$filename = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!isset($allowed[$mime])) {
        echo json_encode(["ok" => false, "error" => "Invalid image type"]);
        exit;
    }

    $ext = $allowed[$mime];
    $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
    $filename = $safe . "_" . time() . ".$ext";
    $dest = __DIR__ . "/uploads/$filename";
    move_uploaded_file($_FILES['image']['tmp_name'], $dest);
}

try {
    if ($filename) {
        $stmt = $pdo->prepare("UPDATE clothes 
                               SET name=?, brand=?, size=?, color=?, price=?, quantity=?, image=? 
                               WHERE id=?");
        $stmt->execute([$name, $brand, $size, $color, $price, $quantity, $filename, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE clothes 
                               SET name=?, brand=?, size=?, color=?, price=?, quantity=? 
                               WHERE id=?");
        $stmt->execute([$name, $brand, $size, $color, $price, $quantity, $id]);
    }
    echo json_encode(["ok" => true]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
