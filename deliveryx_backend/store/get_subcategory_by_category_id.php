<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['category_id'])) {
        $category_id = $_GET['category_id'];

        try {
            $stmt = $con->prepare("SELECT * FROM subcategories WHERE category_id = ?");
            $stmt->execute([$category_id]);

            $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => true,
                'subcategories' => $subcategories
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'category_id is required'
        ]);
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method'
    ]);
}
