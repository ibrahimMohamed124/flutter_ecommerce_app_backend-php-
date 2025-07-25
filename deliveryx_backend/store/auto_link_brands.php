<?php
require_once '../config.php';

try {
    // 1. جلب كل البراندات
    $brandsStmt = $con->prepare("SELECT id, name FROM brands");
    $brandsStmt->execute();
    $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

    $updatedCount = 0;

    foreach ($brands as $brand) {
        $brandId = $brand['id'];
        $brandName = $brand['name'];

        // 2. تحديث المنتجات اللي اسمها يحتوي على اسم البراند
        $updateStmt = $con->prepare("
            UPDATE products 
            SET brand_id = :brand_id
            WHERE name LIKE :name AND (brand_id IS NULL OR brand_id = 0)
        ");
        $updateStmt->execute([
            ':brand_id' => $brandId,
            ':name' => '%' . $brandName . '%',
        ]);

        $affected = $updateStmt->rowCount();
        if ($affected > 0) {
            $updatedCount += $affected;
            echo "✅ تم ربط $affected منتج بالبراند: $brandName\n";
        }
    }

    // 3. تحديث عداد المنتجات داخل جدول brands
    $updateCountStmt = $con->prepare("
        UPDATE brands b
        SET b.products_count = (
            SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id
        )
    ");
    $updateCountStmt->execute();

    echo "🔁 تم تحديث products_count لكل البراندات.\n";
    echo "✅ إجمالي المنتجات التي تم ربطها: $updatedCount\n";

} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage();
}
?>
