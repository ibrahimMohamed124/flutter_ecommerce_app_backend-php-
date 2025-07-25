<?php
header("Content-Type: application/json");
require_once '../../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // ✅ فتح الدرج فقط
    if (isset($data['drawer_id']) && isset($data['order_id']) && isset($data['user_id'])) {
        // مثال: هنا ممكن تحط كود فتح الدرج الحقيقي (تحكم في الهاردوير)
        $drawerId = $data['drawer_id'];
        $espUrl = "http://10.176.181.208/unlock?drawer=$drawerId";
        file_get_contents($espUrl);

        echo json_encode([
            "status" => "success",
            "message" => "تم فتح الدرج"
        ]);
        exit;
    }

    // ✅ تحديث حالة الطلب فقط
    elseif (isset($data['order_id'])) {
        $orderId = $data['order_id'];

        try {
            $stmt = $con->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?");
            $stmt->execute([$orderId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "تم تحديث حالة الطلب"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "لم يتم العثور على الطلب أو لم يتم التعديل"
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "خطأ في قاعدة البيانات: " . $e->getMessage()
            ]);
        }
    }

    // ❌ بيانات غير مكتملة
    else {
        echo json_encode([
            "status" => "error",
            "message" => "البيانات المرسلة غير مكتملة"
        ]);
    }
}

elseif ($method === 'GET') {
    try {
        $stmt = $con->prepare("
            SELECT 
                o.id AS order_id,
                o.status AS order_status,
                u.f_name,
                u.l_name,
                d.drawer_number,
                d.status AS drawer_status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN drawers d ON o.drawer_id = d.id
            JOIN machines m ON d.machine_id = m.id
            WHERE o.status IN ('pending', 'ready')
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "orders" => $orders
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "فشل في جلب الطلبات: " . $e->getMessage()
        ]);
    }
}
