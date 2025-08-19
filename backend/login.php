<?php
require "db.php";

// Accept JSON or form POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (!$data) {
    $data = $_POST; // fallback if JSON not provided
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    json(["ok"=>false,"error"=>"Both fields required", "debug"=>$data]);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        json(["ok"=>false,"error"=>"Invalid email or password"]);
    }

    // Save session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];

    json([
        "ok"=>true,
        "user"=>[
            "id"=>$user['id'],
            "name"=>$user['name'],
            "email"=>$user['email'],
            "role"=>$user['role']
        ]
    ]);
} catch (Exception $e) {
    json(["ok"=>false,"error"=>"Login failed: ".$e->getMessage()]);
}
