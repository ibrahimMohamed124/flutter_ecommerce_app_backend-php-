<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config.php'; // تأكد إن الملف ده فيه الاتصال باستخدام PDO في المتغير $con
// ✅ استقبال البيانات
$data = json_decode(file_get_contents("php://input"), true);

// ✅ التحقق من أن الحقل موجود ومش فاضي
if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode([
        "status" => "error",
        "message" => "الاسم مطلوب"
    ]);
    exit;
}

$name = trim($data['name']);

try {
    $stmt = $con->prepare("INSERT INTO tabs (name) VALUES (:name)");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "تمت إضافة التب بنجاح"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ أثناء الإضافة: " . $e->getMessage()
    ]);
}
