<?php
require __DIR__ . '/db.php';
if (php_sapi_name() !== 'cli') { 
  // optional tiny guard: pass ?key=secret in URL on dev machine
  if (!isset($_GET['key']) || $_GET['key'] !== 'dev_create_admin') { json(["ok"=>false,"error"=>"forbidden"],403); }
}
$name = 'Admin';
$email = 'admin@example.com';
$password = 'Admin@123'; // change locally
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $mysqli->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'admin')");
$stmt->bind_param("sss",$name,$email,$hash);
$ok = $stmt->execute();
if ($ok) json(["ok"=>true,"email"=>$email,"password"=>$password,"note"=>"Delete this file after use"]);
else json(["ok"=>false,"error"=>"Could not create admin (maybe exists)"]);
