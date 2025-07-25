<?php
header("Content-Type: application/json");
require_once '../config.php'; // اتصال قاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// التحقق من البيانات المطلوبة
$requiredFields = ['user_id', 'address', 'payment_method', 'total_amount', 'items'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing field: $field"
        ]);
        exit;
    }
}

$userId        = $data['user_id'];
$address       = $data['address'];
$paymentMethod = $data['payment_method'];
$totalAmount   = $data['total_amount'];
$items         = $data['items']; // مصفوفة المنتجات
$promoCode     = $data['promo_code'] ?? null; // ✅ الكود الترويجي (اختياري)

try {
    // بدء المعاملة
    $con->beginTransaction();

    // 1. اختيار أول درج متاح
    $drawerStmt = $con->prepare("SELECT id FROM drawers WHERE status = 'available' LIMIT 1");
    $drawerStmt->execute();
    $drawer = $drawerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$drawer) {
        $con->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "لا يوجد أدراج متاحة حالياً"
        ]);
        exit;
    }

    $drawerId = $drawer['id'];

    // 2. تغيير حالة الدرج إلى reserved
    $updateDrawer = $con->prepare("UPDATE drawers SET status = 'reserved' WHERE id = ?");
    $updateDrawer->execute([$drawerId]);

    // 3. إدخال الطلب وربط الدرج
    $stmt = $con->prepare("INSERT INTO orders (user_id, address, payment_method, total_amount, created_at, drawer_id) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([$userId, $address, $paymentMethod, $totalAmount, $drawerId]);

    // 4. جلب order_id
    $orderId = $con->lastInsertId();

    // 5. إدخال المنتجات في جدول order_items
    $stmtItem = $con->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, color, size) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmtItem->execute([
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['color'] ?? '',
            $item['size'] ?? ''
        ]);
    }

    // 6. تخزين الكوبون المستخدم إن وُجد
    if (!empty($promoCode)) {
        $insertCoupon = $con->prepare("INSERT INTO used_coupons (user_id, promo_code) VALUES (?, ?)");
        $insertCoupon->execute([$userId, $promoCode]);
    }

    // 7. حذف محتويات السلة
    $stmtDelete = $con->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmtDelete->execute([$userId]);

    // حفظ المعاملة
    $con->commit();

    echo json_encode([
        "status" => "success",
        "message" => "تم تنفيذ الطلب بنجاح",
        "order_id" => $orderId,
        "drawer_id" => $drawerId
    ]);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode([
        "status" => "error",
        "message" => "فشل في تنفيذ الطلب: " . $e->getMessage()
    ]);
}