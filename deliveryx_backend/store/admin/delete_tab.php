<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config.php'; // تأكد إن الملف ده فيه الاتصال باستخدام PDO في المتغير $con

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !is_numeric($data['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "ID غير صالح"
    ]);
    exit;
}

$id = (int)$data['id'];

try {
    $stmt = $con->prepare("DELETE FROM tabs WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "تم حذف التب بنجاح"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ أثناء الحذف: " . $e->getMessage()
    ]);
}
