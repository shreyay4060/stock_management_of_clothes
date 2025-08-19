<?php
require __DIR__."/db.php";
require_admin();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) json(["ok"=>false,"error"=>"Invalid id"],422);

$name = trim($_POST['name'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$size = trim($_POST['size'] ?? '');
$color = trim($_POST['color'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

$filename = null;
if (!empty($_FILES['image']['name'])) {
  $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  $mime = mime_content_type($_FILES['image']['tmp_name']);
  if (!isset($allowed[$mime])) json(["ok"=>false,"error"=>"Invalid image type"],422);

  $ext = $allowed[$mime];
  $safe = clean_filename(pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
  $filename = $safe."_".time().".$ext";
  $dest = __DIR__."/uploads/$filename";
  move_uploaded_file($_FILES['image']['tmp_name'], $dest);
}

if ($filename) {
  $stmt = $mysqli->prepare("UPDATE clothes SET name=?,brand=?,size=?,color=?,price=?,quantity=?,image=? WHERE id=?");
  $stmt->bind_param("ssssdisi",$name,$brand,$size,$color,$price,$quantity,$filename,$id);
} else {
  $stmt = $mysqli->prepare("UPDATE clothes SET name=?,brand=?,size=?,color=?,price=?,quantity=? WHERE id=?");
  $stmt->bind_param("ssssdii",$name,$brand,$size,$color,$price,$quantity,$id);
}
$stmt->execute();
json(["ok"=>true]);
