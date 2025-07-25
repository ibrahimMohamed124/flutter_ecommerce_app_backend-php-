<?php
header("Content-Type: application/json");

// الاتصال بقاعدة البيانات
require_once 'config.php'; // لازم يحتوي على اتصال PDO باسم $pdo

try {
    // استقبال البيانات بصيغة JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // استخراج القيم
    $user_id = $data['user_id'] ?? null;
    $name    = $data['name'] ?? null;
    $phone   = $data['phone'] ?? null;
    $gender  = $data['gender'] ?? null;
    $dob     = $data['dob'] ?? null;

    // التحقق من القيم المطلوبة
    if (!$user_id || !$name || !$phone || !$gender) {
        echo json_encode(["status" => false, "message" => "البيانات غير مكتملة"]);
        exit;
    }

    // تجهيز الاستعلام
    $stmt = $pdo->prepare("UPDATE users SET name = :name, phone = :phone, gender = :gender, dob = :dob WHERE id = :id");

    // تنفيذ الاستعلام
    $stmt->execute([
        ':name'   => $name,
        ':phone'  => $phone,
        ':gender' => $gender,
        ':dob'    => $dob,
        ':id'     => $user_id,
    ]);

    echo json_encode(["status" => true, "message" => "تم تحديث البيانات بنجاح"]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "حدث خطأ: " . $e->getMessage()]);
}
