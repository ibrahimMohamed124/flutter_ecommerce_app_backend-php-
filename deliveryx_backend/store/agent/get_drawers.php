<?php
header("Content-Type: application/json");
require_once '../../config.php';

try {
    $stmt = $con->prepare("SELECT drawer_number, status FROM drawers ORDER BY drawer_number ASC");
    $stmt->execute();
    $drawers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $drawers
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ خطأ في جلب البيانات: ' . $e->getMessage()
    ]);
}
