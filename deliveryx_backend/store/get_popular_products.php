<?php
header("Content-Type: application/json");

include '../config.php'; // تأكد ان هذا الملف يعرف $con كاتصال PDO

$sql = "SELECT * FROM products WHERE is_popular = 1";

try {
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($products) > 0) {
        echo json_encode([
            "success" => true,
            "data" => $products
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No popular products found."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed: " . $e->getMessage()
    ]);
}
?>
