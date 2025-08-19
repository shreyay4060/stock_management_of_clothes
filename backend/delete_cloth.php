<?php
require "db.php";

$id = $_POST['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM clothes WHERE id = ?");
if ($stmt->execute([$id])) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["ok" => false, "error" => "Delete failed"]);
}
