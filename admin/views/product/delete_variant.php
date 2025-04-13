<?php
require_once("../../../includes/db.php");
require_once("../../controller/variantController.php"); // Import controller biến thể

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variant_id = $_POST['variant_id'] ?? '';

    if ($variant_id) {
        $stmt = $conn->prepare("DELETE FROM product_variants WHERE variant_id = ?");
        $stmt->bind_param("s", $variant_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;