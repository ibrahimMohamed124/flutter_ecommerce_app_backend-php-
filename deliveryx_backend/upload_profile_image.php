<?php
error_reporting(0); // إخفاء التحذيرات
ini_set('display_errors', 0);
header("Content-Type: application/json");

// مجلد الصور
$targetDir = "uploads/users/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التأكد من وجود الصورة والـ user_id
    if (!isset($_FILES['profile_image']) || !isset($_POST['user_id'])) {
        echo json_encode(["status" => false, "message" => "البيانات ناقصة"]);
        exit;
    }

    $userId = $_POST['user_id'];
    $fileTmp = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // الصيغ المسموح بها
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => false, "message" => "فقط الصور بصيغة JPG أو PNG أو JPEG مسموح بها"]);
        exit;
    }

    $newFileName = uniqid() . '_' . basename($fileName);
    $targetFile = $targetDir . $newFileName;

    // نقل الصورة للمجلد
    if (move_uploaded_file($fileTmp, $targetFile)) {
        // تحديث قاعدة البيانات
        include 'config.php'; // الملف يحتوي على $con باستخدام PDO

        try {
            $stmt = $con->prepare("UPDATE users SET image = :image WHERE id = :id");
            $stmt->bindParam(':image', $newFileName);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["status" => true, "message" => "تم رفع الصورة بنجاح", "image" => $newFileName]);
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "فشل في رفع الصورة"]);
    }
}
?>
