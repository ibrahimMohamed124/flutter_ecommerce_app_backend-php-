<?php
require_once '../config.php';

header('Content-Type: application/json');

$tabId = isset($_GET['tab_id']) ? intval($_GET['tab_id']) : null;

if ($tabId === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing tab_id"
    ]);
    exit;
}

try {
    // ✅ إضافة top_images إلى الاستعلام
    $stmt = $con->prepare("SELECT id, name, image, products_count, top_images FROM brands WHERE tab_id = ?");
    $stmt->execute([$tabId]);

    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $brands
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Query failed: " . $e->getMessage()
    ]);
}
?>
