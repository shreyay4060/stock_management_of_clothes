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
    json(["ok"=>false,"error"=>"All fields are required"]);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json(["ok"=>false,"error"=>"Email already registered"]);
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $role = "retailer"; // default role
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
    $ok = $stmt->execute([$name, $email, $hash, $role]);

    if ($ok && $stmt->rowCount() > 0) {
        $id = $pdo->lastInsertId();

        // also set session
        $_SESSION['user_id'] = $id;
        $_SESSION['name']    = $name;
        $_SESSION['email']   = $email;
        $_SESSION['role']    = $role;

        json([
            "ok" => true,
            "message" => "Signup successful",
            "user" => [
                "id"    => $id,
                "name"  => $name,
                "email" => $email,
                "role"  => $role
            ]
        ]);
    } else {
        json(["ok"=>false,"error"=>"Insert failed"]);
    }
} catch (Exception $e) {
    json(["ok"=>false,"error"=>"Signup failed: ".$e->getMessage()]);
}
