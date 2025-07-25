<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../config.php'; // الاتصال بقاعدة البيانات

try {
    // استعلام المستخدمين
    $stmt = $con->query("SELECT id, f_name, l_name, user_name, email, phone, role, image, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $users
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch users: " . $e->getMessage()
    ]);
    exit;
}
