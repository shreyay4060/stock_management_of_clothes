<?php
// backend/update_order_status.php
require __DIR__ . "/db.php";
header("Content-Type: application/json");

try {
    $u = require_admin(); // only admin

    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $order_id = intval($body['order_id'] ?? 0);
    $status = trim($body['status'] ?? '');

    if ($order_id <= 0 || !in_array($status, ['pending', 'fulfilled'])) {
        json(["ok"=>false,"error"=>"Invalid input"], 400);
    }

    // update
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    // fetch order + user to email
    $stmt2 = $pdo->prepare("
        SELECT o.id, o.status, u.id AS user_id, u.name AS user_name, u.email AS user_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
        LIMIT 1
    ");
    $stmt2->execute([$order_id]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // send email only when status is 'fulfilled'
        if ($status === 'fulfilled' && !empty($row['user_email'])) {
            $to = $row['user_email'];
            $subject = "Order #{$row['id']} Fulfilled — Clothes Stock";
            $message = "Hello " . ($row['user_name'] ?: 'Customer') . ",\n\n" .
                       "Your order #{$row['id']} has been fulfilled successfully.\n\n" .
                       "Thank you for shopping with Clothes Stock.\n\n" .
                       "Regards,\nClothes Stock Team";
            $headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n" .
                       "Reply-To: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n" .
                       "Content-Type: text/plain; charset=UTF-8\r\n";

            // suppress errors if mail not configured locally
            @mail($to, $subject, $message, $headers);
        }
    }

    json(["ok"=>true,"message"=>"Order updated"]);
} catch (Exception $e) {
    json(["ok"=>false,"error"=>$e->getMessage()], 500);
}
