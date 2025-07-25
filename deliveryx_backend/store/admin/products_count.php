<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once('../../config.php');

try {
    $stmt = $con->query("SELECT COUNT(*) AS total FROM products");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => $count['total']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch product count"
    ]);
}

?>