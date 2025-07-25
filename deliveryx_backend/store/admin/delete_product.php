<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");

require_once('../../config.php');

$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['id'] ?? null;

if (!$product_id) {
    echo json_encode(["status" => "error", "message" => "Product ID required"]);
    exit;
}

try {
    $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);

    echo json_encode(["status" => "success", "message" => "Product deleted"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Failed to delete product"]);
}
