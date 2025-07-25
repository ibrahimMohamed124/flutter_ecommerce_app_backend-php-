<?php
header("Content-Type: application/json");
require_once('../../config.php'); // غيّر المسار حسب مكان الملف

try {
    $stmt = $con->prepare("
        SELECT 
            o.id, 
            o.total_amount, 
            o.status, 
            o.created_at,
            CONCAT(u.f_name, ' ', u.l_name) AS user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error"
    ]);
}
