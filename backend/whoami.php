<?php
require "db.php";

header("Content-Type: application/json");

$user = current_user();

if ($user) {
    echo json_encode([
        "ok"   => true,
        "user" => $user
    ]);
} else {
    echo json_encode([
        "ok"    => false,
        "error" => "Not logged in"
    ]);
}
