<?php
require __DIR__."/db.php";
$u = require_login();

$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
$items = $body['items'] ?? [];

if (!is_array($items) || count($items) === 0) json(["ok"=>false,"error"=>"No items"],422);

$mysqli->begin_transaction();
try {
  $total = 0.0;

  $stmtSel = $mysqli->prepare("SELECT id,name,price,quantity FROM clothes WHERE id=? FOR UPDATE");
  $stmtUpd = $mysqli->prepare("UPDATE clothes SET quantity = quantity - ? WHERE id=?");

  foreach ($items as $it) {
    $cid = intval($it['id'] ?? 0);
    $qty = intval($it['qty'] ?? 0);
    if ($cid<=0 || $qty<=0) throw new Exception("Bad item");

    $stmtSel->bind_param("i",$cid);
    $stmtSel->execute();
    $row = $stmtSel->get_result()->fetch_assoc();
    if (!$row) throw new Exception("Item missing");
    if ($row['quantity'] < $qty) throw new Exception("Insufficient stock for ".$row['name']);

    $total += $row['price'] * $qty;

    $stmtUpd->bind_param("ii",$qty,$cid);
    $stmtUpd->execute();
  }

  $stmtOrder = $mysqli->prepare("INSERT INTO orders(user_id,total,status) VALUES(?,?,'pending')");
  $stmtOrder->bind_param("id",$u['id'],$total);
  $stmtOrder->execute();
  $order_id = $mysqli->insert_id;

  $stmtItem = $mysqli->prepare("INSERT INTO order_items(order_id,cloth_id,quantity,price) VALUES(?,?,?,?)");
  foreach ($items as $it) {
    $cid = intval($it['id']); $qty = intval($it['qty']);
    $p = $mysqli->query("SELECT price FROM clothes WHERE id=".$cid)->fetch_assoc()['price'];
    $stmtItem->bind_param("iiid",$order_id,$cid,$qty,$p);
    $stmtItem->execute();
  }

  $mysqli->commit();
  json(["ok"=>true,"order_id"=>$order_id,"total"=>$total,"status"=>"pending"]);
} catch (Exception $e) {
  $mysqli->rollback();
  json(["ok"=>false,"error"=>$e->getMessage()],400);
}
