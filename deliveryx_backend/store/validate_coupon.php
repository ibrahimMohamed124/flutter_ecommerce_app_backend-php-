<?php
require_once '../config.php';

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'] ?? '';
$user_id = $data['user_id'] ?? null;
$order_total = $data['order_total'] ?? 0;

if (!$code || !$user_id) {
    echo json_encode(["status" => "error", "message" => "بيانات غير مكتملة"]);
    exit;
}

try {
    // تحقق من وجود الكوبون
    $stmt = $con->prepare("SELECT * FROM promo_codes WHERE code = ? AND is_active = 1");
    $stmt->execute([$code]);

    if ($stmt->rowCount() == 0) {
        echo json_encode(["status" => "error", "message" => "الكود غير صالح أو منتهي"]);
        exit;
    }

    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    // تحقق من تاريخ الانتهاء
    if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < time()) {
        echo json_encode(["status" => "error", "message" => "انتهت صلاحية الكوبون"]);
        exit;
    }

    // تحقق من الحد الأدنى
    if ($order_total < $coupon['min_order_amount']) {
        echo json_encode([
            "status" => "error",
            "message" => "الحد الأدنى للطلب هو {$coupon['min_order_amount']} EGP"
        ]);
        exit;
    }

    // تحقق من الاستخدام المسبق
    $checkUsage = $con->prepare("SELECT * FROM used_coupons WHERE user_id = ? AND promo_code = ?");
    $checkUsage->execute([$user_id, $code]);
    if ($checkUsage->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "تم استخدام الكوبون بالفعل"]);
        exit;
    }

    // ✅ صالح
    echo json_encode(["status" => "success", "discount" => $coupon['discount_percent']]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
