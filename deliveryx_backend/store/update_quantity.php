<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $item_id = $data['item_id'] ?? null;
    $new_quantity = $data['quantity'] ?? null;

    if (!$item_id || !$new_quantity) {
        echo json_encode(["status" => "error", "message" => "البيانات غير مكتملة"]);
        exit;
    }

    try {
        $stmt = $con->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $item_id]);

        echo json_encode(["status" => "success", "message" => "تم التحديث بنجاح"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "خطأ: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير مسموح به"]);
}
