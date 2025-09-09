<?php
require __DIR__."/db.php";

header("Content-Type: application/json");

try {
    $u = require_login(true); // ✅ must be logged in via session

    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);
    $items = $body['items'] ?? [];

    if (!is_array($items) || count($items) === 0) {
        throw new Exception("No items in cart");
    }

    $mysqli->begin_transaction();

    $total = 0.0;
    $stmtSel = $mysqli->prepare("SELECT id,name,price,quantity,image FROM clothes WHERE id=? FOR UPDATE");
    $stmtUpd = $mysqli->prepare("UPDATE clothes SET quantity = quantity - ? WHERE id=?");

    foreach ($items as $it) {
        $cid = intval($it['id'] ?? 0);
        $qty = intval($it['qty'] ?? 0);
        if ($cid <= 0 || $qty <= 0) throw new Exception("Bad item");

        $stmtSel->bind_param("i", $cid);
        $stmtSel->execute();
        $row = $stmtSel->get_result()->fetch_assoc();
        if (!$row) throw new Exception("Item missing");
        if ($row['quantity'] < $qty) throw new Exception("Insufficient stock for ".$row['name']);

        $total += $row['price'] * $qty;
        $stmtUpd->bind_param("ii", $qty, $cid);
        $stmtUpd->execute();
    }

    // ✅ Insert order
    $stmtOrder = $mysqli->prepare("INSERT INTO orders(user_id,total,status) VALUES(?,?,'pending')");
    $stmtOrder->bind_param("id", $u['id'], $total);
    $stmtOrder->execute();
    $order_id = $mysqli->insert_id;

    // ✅ Save snapshot into order_items
    $stmtItem = $mysqli->prepare("
        INSERT INTO order_items(order_id,cloth_id,quantity,price,name,image)
        VALUES(?,?,?,?,?,?)
    ");

    foreach ($items as $it) {
        $cid = intval($it['id']);
        $qty = intval($it['qty']);

        $stmtSel->bind_param("i", $cid);
        $stmtSel->execute();
        $row = $stmtSel->get_result()->fetch_assoc();
        if (!$row) throw new Exception("Item missing while inserting order_items");

        $imgFile = $row['image'] ? basename($row['image']) : null; // ✅ only filename

        $stmtItem->bind_param(
            "iiidss",
            $order_id,
            $cid,
            $qty,
            $row['price'],
            $row['name'],
            $imgFile
        );
        $stmtItem->execute();
    }

    $mysqli->commit();
    echo json_encode([
        "ok" => true,
        "order_id" => $order_id,
        "total" => $total,
        "status" => "pending"
    ]);
} catch (Exception $e) {
    if ($mysqli->errno === 0) { // rollback only if transaction is active
        $mysqli->rollback();
    }
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
