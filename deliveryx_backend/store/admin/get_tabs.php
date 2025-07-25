<?php
header("Content-Type: application/json; charset=UTF-8");

require_once '../../config.php'; // تأكد إن الملف ده فيه الاتصال باستخدام PDO في المتغير $con

try {
    $stmt = $con->query("SELECT * FROM tabs ORDER BY id DESC");
    $tabs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $tabs
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ أثناء جلب البيانات: " . $e->getMessage()
    ]);
}
