<?php
require __DIR__."/db.php";
require_admin();

$user_id = intval($_POST['user_id'] ?? 0);
$role = $_POST['role'] ?? 'retailer';
if (!in_array($role, ['retailer','admin'], true) || $user_id <= 0) {
  json(["ok"=>false,"error"=>"Bad params"],422);
}

$stmt = $mysqli->prepare("UPDATE users SET role=? WHERE id=?");
$stmt->bind_param("si",$role,$user_id);
$stmt->execute();

json(["ok"=>true]);
