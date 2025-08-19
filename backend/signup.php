<?php
require "db.php";

// Accept JSON or form POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (!$data) {
    $data = $_POST; // fallback
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    json(["ok"=>false,"error"=>"All fields are required", "debug"=>$data]);
}

// Check if email already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json(["ok"=>false,"error"=>"Email already registered"]);
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    if ($stmt->execute([$name, $email, $hash])) {
        json(["ok"=>true, "message"=>"Signup successful"]);
    } else {
        json(["ok"=>false,"error"=>"Signup failed"]);
    }
} catch (Exception $e) {
    json(["ok"=>false,"error"=>"Signup failed: ".$e->getMessage()]);
}
