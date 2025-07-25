<?php
header("Content-Type: application/json");
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['user_id']) || empty($data['machine_code'])) {
    echo json_encode(["status" => "error", "message" => "Missing user_id or machine_code"]);
    exit;
}

$userId = $data['user_id'];
$machineCode = $data['machine_code'];

try {
    $con->beginTransaction();

    // 1. جلب الماكينة
    $stmt = $con->prepare("SELECT id FROM machines WHERE machine_code = ?");
    $stmt->execute([$machineCode]);
    $machine = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$machine) {
        echo json_encode(["status" => "error", "message" => "المكينة غير موجودة"]);
        exit;
    }

    $machineId = $machine['id'];

    // 2. جلب الطلب
    $stmt = $con->prepare("
        SELECT orders.id AS order_id, drawers.id AS drawer_id, drawers.drawer_number
        FROM orders
        JOIN drawers ON orders.drawer_id = drawers.id
        WHERE orders.user_id = ? AND drawers.machine_id = ? AND orders.status = 'delivered'
        LIMIT 1
    ");
    $stmt->execute([$userId, $machineId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["status" => "error", "message" => "لا يوجد طلب مرتبط بهذه الماكينة"]);
        exit;
    }

    $orderId   = $order['order_id'];
    $drawerId  = $order['drawer_id'];
    $drawerNum = $order['drawer_number'];

    // ✅ 3. إرسال أمر فتح الدرج إلى ESP32 باستخدام curl
    $esp32_ip = "10.176.181.208"; // عدّله حسب IP جهازك
    $esp32_url = "http://$esp32_ip/unlock?drawer=$drawerNum";

    $ch = curl_init($esp32_url);

    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $esp_response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($esp_response === false) {
        throw new Exception("فشل في إرسال أمر الفتح إلى الماكينة: $curl_error");
    }

    // يمكنك التحقق من محتوى الاستجابة:
    $esp_json = json_decode($esp_response, true);
    if (!$esp_json || $esp_json['status'] !== 'success') {
        throw new Exception("الماكينة رفضت الطلب: " . ($esp_json['message'] ?? 'رد غير صالح'));
    }

    // 4. تحديث حالة الدرج → available
    $stmt = $con->prepare("UPDATE drawers SET status = 'available' WHERE id = ?");
    $stmt->execute([$drawerId]);

    // 5. حذف عناصر الطلب من order_items
    $stmt = $con->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);

    // 6. حذف الطلب نفسه من orders
    $stmt = $con->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);

    $con->commit();

    echo json_encode([
        "status" => "success",
        "message" => "تم فتح الدرج وحذف الطلب بنجاح",
        "drawer_number" => $drawerNum
    ]);

} catch (Exception $e) {
    $con->rollBack();
    echo json_encode([
        "status" => "error",
        "message" => "فشل أثناء تنفيذ العملية: " . $e->getMessage()
    ]);
}