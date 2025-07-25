<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // جلب القيم
    $user_id        = $data['user_id'] ?? null;
    $product_id     = $data['product_id'] ?? null;
    $quantity       = $data['quantity'] ?? 1;
    $price          = $data['price'] ?? null;
    $product_title  = $data['product_title'] ?? '';
    $brand_name     = $data['brand_name'] ?? '';
    $image_url      = $data['image_url'] ?? '';
    $color          = $data['color'] ?? '';
    $size           = $data['size'] ?? '';

    // التحقق من البيانات
    if (!$user_id || !$product_id || !$price || empty($product_title) || empty($brand_name) || empty($image_url) || empty($color) || empty($size)) {
        echo json_encode(["status" => "error", "message" => "البيانات غير مكتملة"]);
        exit;
    }

    try {
        // تحقق إذا كان المنتج موجود مسبقًا بنفس اللون والمقاس
        $checkStmt = $con->prepare("
            SELECT id, quantity FROM cart_items
            WHERE user_id = ? AND product_id = ? AND color = ? AND size = ?
        ");
        $checkStmt->execute([$user_id, $product_id, $color, $size]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // تحديث الكمية فقط
            $newQuantity = $existing['quantity'] + $quantity;
            $updateStmt = $con->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $updateStmt->execute([$newQuantity, $existing['id']]);

            echo json_encode(["status" => "success", "message" => "تم تحديث الكمية في السلة"]);
        } else {
            // إضافة المنتج للسلة
            $insertStmt = $con->prepare("
                INSERT INTO cart_items 
                (user_id, product_id, quantity, price, product_title, brand_name, image_url, color, size)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insertStmt->execute([$user_id, $product_id, $quantity, $price, $product_title, $brand_name, $image_url, $color, $size]);

            echo json_encode(["status" => "success", "message" => "تمت إضافة المنتج للسلة"]);
        }

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "خطأ في قاعدة البيانات: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "الطلب غير مسموح به"]);
}
