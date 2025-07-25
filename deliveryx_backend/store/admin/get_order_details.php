<?php
header("Content-Type: application/json; charset=UTF-8");

// استدعاء ملف الاتصال (يحتوي على $con)
require_once '../../config.php';

try {
    $order_id = $_GET['order_id'] ?? null;

    // تحضير الاستعلام باستخدام $con
    if ($order_id) {
        $stmt = $con->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT); // ✅ تم التصحيح هنا
    } else {
        $stmt = $con->prepare("SELECT * FROM order_items");
    }

    // تنفيذ الاستعلام
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // إرسال البيانات كـ JSON
    echo json_encode([
        "status" => "success",
        "data" => $orders
    ]);
} catch (PDOException $e) {
    // في حال وجود خطأ
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء جلب الطلبات: " . $e->getMessage()
    ]);
}
