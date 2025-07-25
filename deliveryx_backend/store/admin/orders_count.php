<?php
header("Content-Type: application/json");

require_once '../../config.php'; // تأكد إن الملف ده فيه الاتصال باستخدام PDO في المتغير $con

try {
    $stmt = $con->query("SELECT COUNT(*) as total FROM orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => $result['total']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "فشل في جلب عدد الطلبات"
    ]);
}
