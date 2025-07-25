<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $stmt = $con->prepare("SELECT * FROM subcategories WHERE category_id = ?");
            $stmt->execute([$category_id]);
        } else {
            $stmt = $con->prepare("SELECT * FROM subcategories");
            $stmt->execute();
        }

        $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => true,
            'subcategories' => $subcategories
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid request method'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
