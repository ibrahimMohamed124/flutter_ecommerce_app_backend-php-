<?php
header("Content-Type: application/json");
require_once '../../../config.php';

try {
    $stmt = $con->prepare("SELECT COUNT(*) AS total FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => $result['total']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ: " . $e->getMessage()
    ]);
}
