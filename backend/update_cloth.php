<?php
// backend/update_cloth.php
require __DIR__ . "/db.php";
require_admin();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "Invalid id"]);
    exit;
}

$name     = trim($_POST['name'] ?? '');
$brand    = trim($_POST['brand'] ?? '');
$size     = trim($_POST['size'] ?? '');
$color    = trim($_POST['color'] ?? '');
$price    = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

$filename = null;

try {
    // If new image uploaded
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

        // Get old image to delete later
        $stmtOld = $pdo->prepare("SELECT image FROM clothes WHERE id=?");
        $stmtOld->execute([$id]);
        $oldRow = $stmtOld->fetch(PDO::FETCH_ASSOC);
        $oldImage = $oldRow['image'] ?? null;

        $ext = $allowed[$mime];
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
        $filename = $safe . "_" . time() . ".$ext";

        $targetDir = __DIR__ . "/uploads";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $dest = $targetDir . "/" . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            throw new Exception("Failed to save uploaded file");
        }

        // Delete old file if it exists
        if ($oldImage && file_exists(__DIR__ . "/uploads/" . $oldImage)) {
            @unlink(__DIR__ . "/uploads/" . $oldImage);
        }

        // Update with new image
        $stmt = $pdo->prepare("UPDATE clothes 
                               SET name=?, brand=?, size=?, color=?, price=?, quantity=?, image=? 
                               WHERE id=?");
        $stmt->execute([$name, $brand, $size, $color, $price, $quantity, $filename, $id]);

    } else {
        // Update without image
        $stmt = $pdo->prepare("UPDATE clothes 
                               SET name=?, brand=?, size=?, color=?, price=?, quantity=? 
                               WHERE id=?");
        $stmt->execute([$name, $brand, $size, $color, $price, $quantity, $id]);
    }

    // Return updated row
    $stmt2 = $pdo->prepare("SELECT * FROM clothes WHERE id=?");
    $stmt2->execute([$id]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['image'])) {
        $row['image'] = "backend/uploads/" . $row['image'];
    } else {
        $row['image'] = null;
    }

    echo json_encode(["ok" => true, "cloth" => $row]);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
