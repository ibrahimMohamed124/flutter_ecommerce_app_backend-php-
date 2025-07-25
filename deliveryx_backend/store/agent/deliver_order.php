<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../config.php'; // ملف الاتصال

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // ✅ جلب الطلبات
    try {
        $stmt = $con->prepare("
            SELECT 
                o.id AS order_id,
                o.status AS order_status,
                u.f_name,
                u.l_name,
                d.drawer_number,
                d.status AS drawer_status,
                m.machine_code
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

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // ✅ تسليم الطلب فقط
    if (isset($data['order_id']) && !isset($data['drawer_id']) && !isset($data['user_id'])) {
        $orderId = $data['order_id'];
        if (!$orderId) {
            echo json_encode([
                "status" => "error",
                "message" => "رقم الطلب غير موجود"
            ]);
            exit;
        }

        try {
            $stmt = $con->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?");
            $stmt->execute([$orderId]);

            echo json_encode([
                "status" => "success",
                "message" => "تم تسليم الطلب بنجاح"
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "فشل في تسليم الطلب: " . $e->getMessage()
            ]);
        }

    }
    // ✅ فتح الدرج
    elseif (!empty($data['drawer_id']) && !empty($data['order_id']) && !empty($data['user_id'])) {
        $drawer_id = $data['drawer_id'];
        $order_id  = $data['order_id'];
        $user_id   = $data['user_id'];

        try {
            $stmtCheck = $con->prepare("
                SELECT d.drawer_number, d.status AS drawer_status
                FROM drawers d
                JOIN orders o ON o.drawer_id = d.id
                WHERE d.id = ? AND o.id = ? AND o.user_id = ?
            ");
            $stmtCheck->execute([$drawer_id, $order_id, $user_id]);

            if ($stmtCheck->rowCount() == 0) {
                echo json_encode(["status" => "error", "message" => "الطلب أو الدرج غير موجود أو غير مرتبط ببعض"]);
                exit;
            }

            $drawer = $stmtCheck->fetch();
            $drawerNum = $drawer['drawer_number'];

            if ($drawer['drawer_status'] === 'open') {
                echo json_encode(["status" => "error", "message" => "الدرج مفتوح بالفعل"]);
                exit;
            }

            // ✅ تحديث حالة الدرج إلى "open"
            $stmtUpdate = $con->prepare("UPDATE drawers SET status = 'open' WHERE id = ?");
            $stmtUpdate->execute([$drawer_id]);

            // ✅ إرسال أمر فتح فعلي إلى ESP32
            $esp32_ip = "192.168.205.208"; // IP جهاز ESP32
            $esp32_url = "http://$esp32_ip/unlock?drawer=$drawerNum";

            $ch = curl_init($esp32_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                echo json_encode([
                    "status" => "error",
                    "message" => "فشل في الاتصال بـ ESP32: $error"
                ]);
                exit;
            }

            $esp_json = json_decode($response, true);
            if (!$esp_json || $esp_json['status'] !== 'success') {
                echo json_encode([
                    "status" => "error",
                    "message" => "فشل في فتح الدرج من ESP: " . ($esp_json['message'] ?? 'رد غير صالح')
                ]);
                exit;
            }

            echo json_encode([
                "status" => "success",
                "message" => "تم فتح الدرج بنجاح"
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "حدث خطأ أثناء فتح الدرج: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "معلمات غير مكتملة"
        ]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
