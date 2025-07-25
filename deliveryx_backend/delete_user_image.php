<?php
header("Content-Type: application/json");
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => false, "message" => "Invalid request"]);
    exit;
}

$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if ($userId === 0) {
    echo json_encode(["status" => false, "message" => "User ID is required"]);
    exit;
}

try {
    // الحصول على اسم الصورة الحالية
    $stmt = $con->prepare("SELECT image FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["status" => false, "message" => "User not found"]);
        exit;
    }

    $imagePath = $row['image'];
    
    // مسح الصورة من السيرفر (لو كانت صورة مرفوعة وليست من placeholder خارجي)
    if (!empty($imagePath) && file_exists("../$imagePath")) {
        unlink("../$imagePath");
    }

    // تحديث الصورة لتكون null أو placeholder
    $stmt = $con->prepare("UPDATE users SET image = NULL WHERE id = ?");
    $stmt->execute([$userId]);

    echo json_encode(["status" => true, "message" => "Profile image deleted successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => false, "message" => "Database error: " . $e->getMessage()]);
}
