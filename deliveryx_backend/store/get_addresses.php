<?php
header("Content-Type: application/json");
require_once '../config.php'; // الاتصال بقاعدة البيانات

$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    try {
        $stmt = $con->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $addresses]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "DB error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
}
