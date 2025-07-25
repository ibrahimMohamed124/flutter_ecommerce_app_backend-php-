<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");

require_once('../../config.php');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$name = htmlspecialchars(trim($data['name'] ?? ''));
$brand = htmlspecialchars(trim($data['brand'] ?? ''));
$description = htmlspecialchars(trim($data['description'] ?? ''));
$image = htmlspecialchars(trim($data['image'] ?? ''));
$price = $data['price'] ?? 0;
$discount = $data['discount'] ?? 0;
$discount_price = $data['discount_price'] ?? 0;
$colors = $data['colors'] ?? '';
$sizes = $data['sizes'] ?? '';
$images = $data['images'] ?? '';
$category_id = $data['category_id'] ?? null;
$is_popular = $data['is_popular'] ?? 0;

if (!$id || !$name || !$price || !$category_id) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $con->prepare("UPDATE products SET name=?, brand=?, description=?, image=?, price=?, discount=?, discount_price=?, colors=?, sizes=?, images=?, category_id=?, is_popular=? WHERE id=?");
    $stmt->execute([$name, $brand, $description, $image, $price, $discount, $discount_price, $colors, $sizes, $images, $category_id, $is_popular, $id]);

    echo json_encode(["status" => "success", "message" => "Product updated"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Failed to update product"]);
}
