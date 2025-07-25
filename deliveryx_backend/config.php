<?php
header("Content-Type: application/json; charset=UTF-8");

$host = 'localhost';
$db   = 'smart_locker';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $con = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "DB connection failed: " . $e->getMessage()
    ]);
    exit;
}
