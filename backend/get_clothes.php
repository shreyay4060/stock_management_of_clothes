<?php
require "db.php";

$baseUrl = "http://localhost/stock_management_of_clothes/backend/uploads/";

$stmt = $pdo->query("SELECT * FROM clothes ORDER BY created_at DESC");
$clothes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add full path for image
foreach ($clothes as &$c) {
    if ($c['image']) {
        $c['image'] = $baseUrl . $c['image'];
    } else {
        $c['image'] = "../images/arrival3.jpg"; // fallback
    }
}

echo json_encode(["ok" => true, "clothes" => $clothes]);
