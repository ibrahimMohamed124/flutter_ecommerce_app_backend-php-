<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "User ID مطلوب"]);
        exit;
    }

    try {
        // جلب عناصر السلة
        $stmt = $con->prepare("
            SELECT 
                id,
                user_id,
                product_id,
                quantity,
                price,
                product_title,
                brand_name,
                image_url,
                color,
                size,
                (price * quantity) AS total
            FROM cart_items
            WHERE user_id = ?
        ");

        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // حساب إجمالي السلة (كقيمة عددية بدون تنسيق)
        $totalStmt = $con->prepare("SELECT SUM(price * quantity) AS cart_total FROM cart_items WHERE user_id = ?");
        $totalStmt->execute([$user_id]);
        $cartTotal = $totalStmt->fetch(PDO::FETCH_ASSOC)['cart_total'] ?? 0;

        echo json_encode([
            "status" => "success",
            "data" => $items,
            "cart_total" => round((float)$cartTotal, 2)  // ✅ بدون فواصل، فقط رقم عشري
        ]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "فشل في جلب البيانات: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير مسموح به"]);
}
