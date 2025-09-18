<?php
require __DIR__."/db.php";

header("Content-Type: application/json");

try {
    $u = require_login(true); // ✅ must be logged in

    $stmt = $pdo->prepare("SELECT id, name, phone, address_line1, address_line2, city, state, pincode 
                           FROM addresses WHERE user_id=? LIMIT 1");
    $stmt->execute([$u['id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($address) {
        json([
            "ok" => true,
            "address" => $address
        ]);
    } else {
        json([
            "ok" => true,   // ✅ return ok:true (so frontend doesn’t break)
            "address" => null
        ]);
    }
} catch (Exception $e) {
    json([
        "ok" => false,
        "error" => "Error fetching address: ".$e->getMessage()
    ]);
}
