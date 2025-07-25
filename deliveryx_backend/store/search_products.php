<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config.php'; // تأكد إنه فيه $con

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $query = $input['query'] ?? '';

    if (empty($query)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'يرجى إدخال كلمة البحث'
        ]);
        exit;
    }

    $stmt = $con->prepare("SELECT * FROM products WHERE name LIKE :query AND is_popular = 1");
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $results
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'حدث خطأ أثناء تنفيذ البحث: ' . $e->getMessage()
    ]);
}
