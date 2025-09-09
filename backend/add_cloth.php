<?php
// backend/add_cloth.php
require __DIR__ . "/db.php";
require_admin(); // only admins should add cloths

$name = $_POST['name'] ?? '';
$brand = $_POST['brand'] ?? '';
$size = $_POST['size'] ?? '';
$color = $_POST['color'] ?? '';
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$imageFilename = null;

try {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // sanitize filename
        $orig = basename($_FILES['image']['name']);
        $safe = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $imageFilename = time() . "_" . $safe . "." . $ext;

        $targetDir = __DIR__ . "/uploads";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $target = $targetDir . "/" . $imageFilename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            throw new Exception("Failed to move uploaded file");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO clothes (name, brand, size, color, price, quantity, image, created_at)
                           VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $ok = $stmt->execute([$name, $brand, $size, $color, $price, $quantity, $imageFilename]);

    if ($ok) {
        $id = $pdo->lastInsertId();
        // return the inserted row
        $stmt2 = $pdo->prepare("SELECT * FROM clothes WHERE id = ?");
        $stmt2->execute([$id]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['image'])) {
            $row['image'] = "backend/uploads/" . $row['image'];
        } else {
            $row['image'] = null;
        }
        echo json_encode(["ok" => true, "cloth" => $row]);
        exit;
    } else {
        echo json_encode(["ok" => false, "error" => "DB insert failed"]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
    exit;
}
