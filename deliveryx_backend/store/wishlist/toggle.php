<?php
header("Content-Type: application/json");
require_once '../../config.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$product_id = $data['product_id'] ?? null;

if (!$user_id || !$product_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing user_id or product_id"
    ]);
    exit;
}

try {
    // هل المنتج موجود بالفعل في المفضلة؟
    $checkStmt = $con->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$user_id, $product_id]);

    if ($checkStmt->rowCount() > 0) {
        // موجود → نحذفه
        $deleteStmt = $con->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $deleteStmt->execute([$user_id, $product_id]);

        echo json_encode([
            "status" => "success",
            "action" => "removed"
        ]);
    } else {
        // مش موجود → نضيفه
        $insertStmt = $con->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insertStmt->execute([$user_id, $product_id]);

        echo json_encode([
            "status" => "success",
            "action" => "added"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
