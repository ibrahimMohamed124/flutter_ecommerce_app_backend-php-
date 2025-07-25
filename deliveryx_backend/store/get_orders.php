<?php
header("Content-Type: application/json");
require_once '../config.php'; // الاتصال بقاعدة البيانات

// التحقق من user_id
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing user_id"
    ]);
    exit;
}

$user_id = $_GET['user_id'];
$statusFilter = $_GET['status'] ?? null;
$dateFilter = $_GET['date'] ?? null; // بصيغة YYYY-MM-DD

try {
    // بناء الاستعلام بشكل ديناميكي حسب الفلاتر
    $query = "
        SELECT o.id AS order_id, o.total_amount, o.payment_method, o.created_at, o.address, o.status,
               oi.product_id, oi.quantity, oi.price, oi.color, oi.size
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = :user_id
    ";

    $params = [':user_id' => $user_id];

    if (!empty($statusFilter)) {
        $query .= " AND o.status = :status";
        $params[':status'] = $statusFilter;
    }

    if (!empty($dateFilter)) {
        $query .= " AND DATE(o.created_at) = :created_date";
        $params[':created_date'] = $dateFilter;
    }

    $query .= " ORDER BY o.created_at DESC";

    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    if (!$rows) {
        echo json_encode([
            "status" => "success",
            "data" => [],
            "message" => "لا توجد طلبات بهذه المعايير"
        ]);
        exit;
    }

    // تنظيم النتائج حسب الطلبات
    $orders = [];
    foreach ($rows as $row) {
        $orderId = $row['order_id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                "order_id"       => $orderId,
                "status"         => $row['status'],
                "address"        => $row['address'],
                "payment_method" => $row['payment_method'],
                "total_amount"   => $row['total_amount'],
                "created_at"     => $row['created_at'],
                "items"          => []
            ];
        }

        $orders[$orderId]['items'][] = [
            "product_id" => $row['product_id'],
            "quantity"   => $row['quantity'],
            "price"      => $row['price'],
            "color"      => $row['color'],
            "size"       => $row['size']
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => array_values($orders)
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "فشل في جلب الطلبات: " . $e->getMessage()
    ]);
}
