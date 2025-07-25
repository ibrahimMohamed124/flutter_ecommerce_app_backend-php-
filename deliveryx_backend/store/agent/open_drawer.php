<?php
header("Content-Type: application/json");
require_once '../../config.php';

$data = json_decode(file_get_contents("php://input"));
if (!isset($data->locker_id)) {
    echo json_encode(['status' => 'error', 'message' => ' locker_id مطلوب']);
    exit;
}

$drawerId = $data->locker_id;

try {
    $stmt = $con->prepare("SELECT status FROM drawers WHERE drawer_number = ?");
    $stmt->execute([$drawerId]);
    $drawer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$drawer || $drawer['status'] !== 'available') {
        echo json_encode(['status' => 'error', 'message' => '⚠️ الدرج غير متاح حاليًا']);
        exit;
    }

    // هنا مثال للـ ESP URL - غيّر IP حسب جهازك
    $espUrl = "http://192.168.205.208/unlock?drawer=$drawerId";
    file_get_contents($espUrl);

    $update = $con->prepare("UPDATE drawers SET status = 'open' WHERE drawer_number = ?");
    $update->execute([$drawerId]);

    echo json_encode(['status' => 'success', 'message' => '✅ تم فتح الدرج بنجاح']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => '❌ خطأ: ' . $e->getMessage()]);
}
