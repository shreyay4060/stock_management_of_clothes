<?php
require __DIR__."/db.php";

$u = require_admin(); // ✅ only admin can access

$stmt = $pdo->query("SELECT o.*, u.name AS user_name, u.email AS user_email 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$out = [];
foreach ($orders as $o) {
    $stmt2 = $pdo->prepare("SELECT name, image, quantity, price 
                            FROM order_items WHERE order_id = ?");
    $stmt2->execute([$o['id']]);
    $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $out[] = [
        "id" => $o['id'],
        "user" => [
            "id"    => $o['user_id'],
            "name"  => $o['user_name'],
            "email" => $o['user_email']
        ],
        "total" => $o['total'],
        "status" => $o['status'],
        "created_at" => $o['created_at'],
        "items" => array_map(function($i){
            return [
                "name" => $i['name'],
                "quantity" => $i['quantity'],
                "price" => $i['price'],
                "image" => $i['image'] ? "backend/uploads/".$i['image'] : "images/arrival3.jpg"
            ];
        }, $items)
    ];
}

echo json_encode(["ok"=>true,"orders"=>$out]);
