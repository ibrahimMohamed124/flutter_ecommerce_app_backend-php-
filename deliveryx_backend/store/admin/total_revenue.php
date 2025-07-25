<?php
header("Content-Type: application/json");
require_once '../../config.php';

try {
    $stmt = $con->query("SELECT SUM(total_amount) AS revenue FROM orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "revenue" => $result['revenue'] ?? 0
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "فشل في جلب الإيرادات"
    ]);
}
?>
