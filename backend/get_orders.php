<?php
// backend/get_orders.php
require __DIR__."/db.php";
header("Content-Type: application/json");

try {
    $u = require_login(true); // require logged in (API)

    // fetch user's orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$u['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = [];

    function normalizeImagePath($img) {
        if (!$img) return "images/arrival3.jpg";
        if (strpos($img, "http") === 0 || strpos($img, "backend/") === 0 || strpos($img, "uploads/") === 0) {
            return $img;
        }
        return "backend/uploads/" . $img;
    }

    foreach ($orders as $o) {
        $stmt2 = $pdo->prepare("SELECT name, image, quantity, price FROM order_items WHERE order_id = ?");
        $stmt2->execute([$o['id']]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // fetch address (if any)
        $addr = null;
        if (!empty($o['address_id'])) {
            $s3 = $pdo->prepare("SELECT name,phone,address_line1,address_line2,city,state,pincode FROM addresses WHERE id = ? LIMIT 1");
            $s3->execute([$o['address_id']]);
            $addr = $s3->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $out[] = [
            "id" => $o['id'],
            "total" => $o['total'],
            "status" => $o['status'],
            "created_at" => $o['created_at'],
            "address" => $addr,
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
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$e->getMessage()]);
}
