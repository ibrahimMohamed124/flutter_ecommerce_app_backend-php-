<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// الاتصال بقاعدة البيانات باستخدام PDO
require_once '../deliveryx_backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        echo json_encode(['status' => false, 'message' => 'User ID is required']);
        exit();
    }

    $user_id = intval($data['user_id']);

    try {
        $stmt = $con->prepare("SELECT id, f_name, l_name, user_name, email, phone, role, created_at, image FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user) {
            $response = [
                'id' => $user['id'],
                'f_name' => $user['f_name'],
                'l_name' => $user['l_name'],
                'user_name' => $user['user_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role'],
                'created_at' => $user['created_at'],
                'image' => !empty($user['image']) ? $user['image'] : null,
            ];

            echo json_encode(['status' => true, 'data' => $response]);
        } else {
            echo json_encode(['status' => false, 'message' => 'User not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'message' => 'Database query error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}
