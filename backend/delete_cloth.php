<?php
// backend/delete_cloth.php
require __DIR__ . "/db.php";
require_admin();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) json(["ok" => false, "error" => "Invalid id"], 422);

try {
    // fetch image name
    $stmt = $pdo->prepare("SELECT image FROM clothes WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $image = $row['image'] ?? null;

    // delete DB record
    $stmt2 = $pdo->prepare("DELETE FROM clothes WHERE id = ?");
    $ok = $stmt2->execute([$id]);
    if ($ok) {
        if ($image) {
            $path = __DIR__ . "/uploads/" . $image;
            if (file_exists($path)) @unlink($path);
        }
        json(["ok" => true]);
    } else {
        json(["ok" => false, "error" => "Delete failed"]);
    }
} catch (Exception $e) {
    json(["ok" => false, "error" => $e->getMessage()]);
}
