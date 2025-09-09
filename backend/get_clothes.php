<?php
// backend/get_clothes.php
require __DIR__ . "/db.php";

try {
    $stmt = $pdo->query("SELECT * FROM clothes ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$r) {
        if (!empty($r['image'])) {
            if (!str_starts_with($r['image'], "backend/uploads/") && !str_starts_with($r['image'], "http") && !str_starts_with($r['image'], "images/")) {
                $r['image'] = "backend/uploads/" . $r['image'];
            }
        } else {
            $r['image'] = "images/arrival3.jpg";
        }
    }

    echo json_encode(["ok" => true, "clothes" => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
