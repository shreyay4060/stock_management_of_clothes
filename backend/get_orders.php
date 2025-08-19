<?php
require __DIR__."/db.php";
$u = require_login();

$all = isset($_GET['all']) && $_GET['all']=='1' && $u['role']==='admin';

if ($all) {
  $sql = "SELECT o.id,o.user_id,u.name as user_name,o.total,o.status,o.created_at
          FROM orders o JOIN users u ON u.id=o.user_id
          ORDER BY o.created_at DESC";
  $res = $mysqli->query($sql);
} else {
  $stmt = $mysqli->prepare("SELECT id,total,status,created_at FROM orders WHERE user_id=? ORDER BY created_at DESC");
  $stmt->bind_param("i",$u['id']); $stmt->execute(); $res = $stmt->get_result();
}

$orders = [];
while ($row = $res->fetch_assoc()) {
  $oid = intval($row['id']);
  $itres = $mysqli->query("SELECT oi.quantity,oi.price,c.name,c.image FROM order_items oi
                           JOIN clothes c ON c.id=oi.cloth_id WHERE oi.order_id=".$oid);
  $row['items'] = $itres->fetch_all(MYSQLI_ASSOC);
  $orders[] = $row;
}
json(["ok"=>true,"orders"=>$orders]);
