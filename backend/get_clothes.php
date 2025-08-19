<?php
require "db.php";

$stmt = $pdo->query("SELECT * FROM clothes ORDER BY created_at DESC");
$clothes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["ok" => true, "clothes" => $clothes]);
