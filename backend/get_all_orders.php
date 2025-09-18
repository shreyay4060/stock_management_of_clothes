<?php
require __DIR__."/db.php";

header("Content-Type: application/json");

try {
    $u = require_admin(); // ✅ only admin can access

    $stmt = $pdo->query("
        SELECT o.*, u.name AS user_name, u.email AS user_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = [];

    // normalize image paths like in other files
    function normalizeImagePath($img) {
        if (!$img) return "images/arrival3.jpg";
        if (strpos($img, "http") === 0 || strpos($img, "backend/") === 0 || strpos($img, "uploads/") === 0) {
            return $img;
        }
        return "backend/uploads/" . $img;
    }

    foreach ($orders as $o) {
        // fetch order items
        $stmt2 = $pdo->prepare("SELECT name, image, quantity, price FROM order_items WHERE order_id = ?");
        $stmt2->execute([$o['id']]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // fetch address (if exists)
        $address = null;
        if (!empty($o['address_id'])) {
            $stmt3 = $pdo->prepare("SELECT name, phone, address_line1, address_line2, city, state, pincode 
                                    FROM addresses WHERE id = ? LIMIT 1");
            $stmt3->execute([$o['address_id']]);
            $address = $stmt3->fetch(PDO::FETCH_ASSOC) ?: null;
        }

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
            "address" => $address,
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
} catch (Exception $e) {
    echo json_encode(["ok"=>false,"error"=>$e->getMessage()]);
}
