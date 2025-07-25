<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once('../../config.php');

$data = json_decode(file_get_contents("php://input"), true);

$title = htmlspecialchars(trim($data['name'] ?? ''));
$brand = htmlspecialchars(trim($data['brand'] ?? ''));
$description = htmlspecialchars(trim($data['description'] ?? ''));
$image = htmlspecialchars(trim($data['image'] ?? ''));
$price = $data['price'] ?? 0;
$discount = $data['discount'] ?? 0;
$discount_price = $data['discount_price'] ?? 0;
$colors = htmlspecialchars(trim($data['colors'] ?? ''));
$sizes = htmlspecialchars(trim($data['sizes'] ?? ''));
$images = htmlspecialchars(trim($data['images'] ?? ''));
$category_id = $data['category_id'] ?? null;
$is_popular = $data['is_popular'] ?? 0;

if (empty($title) || $price <= 0 || empty($category_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

try {
    $stmt = $con->prepare("INSERT INTO products (name, brand, description, image, price, discount, discount_price, colors, sizes, images, category_id, is_popular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $brand, $description, $image, $price, $discount, $discount_price, $colors, $sizes, $images, $category_id, $is_popular]);

    echo json_encode(["status" => "success", "message" => "Product added"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Failed to add product"]);
}
