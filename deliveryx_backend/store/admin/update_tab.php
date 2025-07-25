<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config.php'; // تأكد إن الملف ده فيه الاتصال باستخدام PDO في المتغير $con

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode([
        "status" => "error",
        "message" => "البيانات غير كاملة"
    ]);
    exit;
}

$id = (int)$data['id'];
$name = trim($data['name']);

try {
    $stmt = $con->prepare("UPDATE tabs SET name = :name WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "تم تحديث اسم التب بنجاح"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ أثناء التعديل: " . $e->getMessage()
    ]);
}
