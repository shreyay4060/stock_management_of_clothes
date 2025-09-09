<?php
require __DIR__."/db.php";
$u = require_login(true);

$stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id=? LIMIT 1");
$stmt->execute([$u['id']]);
$addr = $stmt->fetch(PDO::FETCH_ASSOC);

if ($addr) {
    json(["ok"=>true,"address"=>$addr]);
} else {
    json(["ok"=>false,"error"=>"No address saved"]);
}
