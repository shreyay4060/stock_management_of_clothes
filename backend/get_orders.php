<?php
require __DIR__."/db.php";
$u = require_login();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$u['id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$out = [];

function normalizeImagePath($img) {
    if (!$img) return "images/arrival3.jpg"; // fallback
    if (strpos($img, "http") === 0 || strpos($img, "backend/") === 0 || strpos($img, "uploads/") === 0) {
        return $img;
    }
    return "backend/uploads/" . $img;
}

foreach ($orders as $o) {
    $stmt2 = $pdo->prepare("SELECT name, image, quantity, price FROM order_items WHERE order_id = ?");
    $stmt2->execute([$o['id']]);
    $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $out[] = [
        "id" => $o['id'],
        "total" => $o['total'],
        "status" => $o['status'],
        "created_at" => $o['created_at'],
        "items" => array_map(function($i){
            return [
                "name" => $i['name'],
                "quantity" => $i['quantity'],
                "price" => $i['price'],
                "image" => normalizeImagePath($i['image'])
            ];
        }, $items)
    ];
}

echo json_encode(["ok"=>true,"orders"=>$out]);
