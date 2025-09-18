<?php
require __DIR__."/db.php";
$u = require_login(true);

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$line1 = trim($data['address_line1'] ?? '');
$line2 = trim($data['address_line2'] ?? '');
$city = trim($data['city'] ?? '');
$state = trim($data['state'] ?? '');
$pincode = trim($data['pincode'] ?? '');

if (!$name || !$phone || !$line1 || !$city || !$state || !$pincode) {
    json(["ok"=>false,"error"=>"All required fields must be filled"]);
}

try {
    // Check if address exists for this user
    $stmt = $pdo->prepare("SELECT id FROM addresses WHERE user_id=? LIMIT 1");
    $stmt->execute([$u['id']]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        // ✅ Update existing address
        $stmt = $pdo->prepare("
            UPDATE addresses 
            SET name=?, phone=?, address_line1=?, address_line2=?, city=?, state=?, pincode=?, updated_at=NOW()
            WHERE id=? AND user_id=?
        ");
        $stmt->execute([$name,$phone,$line1,$line2,$city,$state,$pincode,$exists['id'],$u['id']]);
        $addr_id = $exists['id'];
    } else {
        // ✅ Insert new address
        $stmt = $pdo->prepare("
            INSERT INTO addresses(user_id,name,phone,address_line1,address_line2,city,state,pincode,created_at,updated_at) 
            VALUES(?,?,?,?,?,?,?,?,NOW(),NOW())
        ");
        $stmt->execute([$u['id'],$name,$phone,$line1,$line2,$city,$state,$pincode]);
        $addr_id = $pdo->lastInsertId();
    }

    json(["ok"=>true,"address_id"=>$addr_id]);

} catch (Exception $e) {
    json(["ok"=>false,"error"=>$e->getMessage()]);
}
