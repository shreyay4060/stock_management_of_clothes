<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$DB_HOST = "127.0.0.1";
$DB_USER = "root";   // change if needed
$DB_PASS = "";       // set your MySQL password
$DB_NAME = "clothes_stock";

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // mysqli also (for existing code using $mysqli)
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($mysqli->connect_errno) {
        throw new Exception("MySQLi connect error: " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["ok"=>false,"error"=>"DB connection failed: ".$e->getMessage()]);
    exit;
}

function json($arr, $code=200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

function current_user() {
    if (!isset($_SESSION['user_id'])) return null;
    return [
        "id" => $_SESSION['user_id'],
        "name" => $_SESSION['name'] ?? '',
        "email" => $_SESSION['email'] ?? '',
        "role" => $_SESSION['role'] ?? 'retailer'
    ];
}

function require_login($api = false) {
    if (!isset($_SESSION['user_id'])) {
        if ($api) {
            header("Content-Type: application/json");
            echo json_encode(["ok" => false, "error" => "Not logged in"]);
            exit;
        } else {
            header("Location: ../index.php");
            exit;
        }
    }

    return [
        "id"    => $_SESSION['user_id'],
        "name"  => $_SESSION['name'] ?? '',
        "email" => $_SESSION['email'] ?? '',
        "role"  => $_SESSION['role'] ?? 'retailer'
    ];
}


function require_admin() {
    $u = require_login();
    if (($u['role'] ?? 'retailer') !== 'admin') {
        json(["ok"=>false,"error"=>"Admins only"], 403);
    }
    return $u;
}

function clean_filename($name) {
    return preg_replace('/[^a-zA-Z0-9_\.-]/','_', $name);
}
