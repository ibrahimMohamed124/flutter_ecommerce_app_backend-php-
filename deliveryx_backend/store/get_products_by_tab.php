<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['tab_id'])) {
        $tabId = intval($_GET['tab_id']);

        try {
            $stmt = $con->prepare("
                SELECT p.*, b.tab_id
                FROM products p
                JOIN brands b ON p.brand_id = b.id
                WHERE b.tab_id = :tab_id
            ");
            $stmt->execute(['tab_id' => $tabId]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $products
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "خطأ في جلب المنتجات: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "يرجى تحديد tab_id"
        ]);
    }
}
?>
