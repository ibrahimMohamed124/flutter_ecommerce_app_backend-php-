<?php
header("Content-Type: application/json; charset=UTF-8");

// استدعاء ملف الاتصال (يحتوي على $con)
require_once '../../config.php';

try {
    $user_id = $_GET['user_id'] ?? null;

    // تحضير الاستعلام باستخدام $con
    if ($user_id) {
        $stmt = $con->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    } else {
        $stmt = $con->prepare("SELECT * FROM orders");
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
