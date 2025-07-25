<?php
header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
try {
   include 'config.php'; // تأكد من وجود ملف config.php الذي يحتوي على إعدادات الاتصال بقاعدة البيانات

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'] ?? null;

        if ($id) {
            $stmt = $con->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "User deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete user"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "User ID is required"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "DB connection failed: " . $e->getMessage()]);
}
