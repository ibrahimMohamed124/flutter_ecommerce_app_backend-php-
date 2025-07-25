<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include '../config.php'; // يجب أن يحتوي على $dsn, $user, $pass, $options

try {
    if (isset($_GET['product_id'])) {
        $productId = $_GET['product_id'];
        $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
    } else {
        $stmt = $con->query("SELECT * FROM products");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch products: " . $e->getMessage()
    ]);
}
?>
