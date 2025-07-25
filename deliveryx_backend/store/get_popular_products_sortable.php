<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config.php'; // الاتصال بقاعدة البيانات

try {
    $sort_by = $_GET['sort_by'] ?? 'popularity'; // الافتراضي: popularity

    $allowedSorts = [
        'name' => 'name ASC',
        'low_to_high' => 'price ASC',
        'high_to_low' => 'price DESC',
        'newest' => 'created_at DESC',
        'oldest' => 'created_at ASC',
        'popularity' => 'is_popular DESC, rating DESC'
    ];

    $orderBy = $allowedSorts[$sort_by] ?? $allowedSorts['popularity'];

    $stmt = $con->prepare("SELECT * FROM products WHERE is_popular = 1 ORDER BY $orderBy");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "حدث خطأ أثناء جلب المنتجات: " . $e->getMessage()
    ]);
}
