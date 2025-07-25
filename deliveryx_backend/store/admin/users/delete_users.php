<?php
header("Content-Type: application/json");
require_once '../../../config.php'; // ملف الاتصال

// تأكد من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

// استقبل البيانات بصيغة JSON
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    echo json_encode([
        "status" => "error",
        "message" => "User ID is required"
    ]);
    exit;
}

try {
    // تنفيذ الحذف باستخدام PDO
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "تم حذف المستخدم بنجاح"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "لم يتم العثور على المستخدم أو لم يتم حذفه"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء الحذف: " . $e->getMessage()
    ]);
}
