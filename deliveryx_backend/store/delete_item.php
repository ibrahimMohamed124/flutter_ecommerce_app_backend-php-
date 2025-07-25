<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $item_id = $data['item_id'] ?? null;

    if (!$item_id) {
        echo json_encode(["status" => "error", "message" => "رقم المنتج مطلوب"]);
        exit;
    }

    try {
        $stmt = $con->prepare("DELETE FROM cart_items WHERE id = ?");
        $stmt->execute([$item_id]);

        echo json_encode(["status" => "success", "message" => "تم حذف المنتج"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "فشل في حذف المنتج: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير مسموح به"]);
}
