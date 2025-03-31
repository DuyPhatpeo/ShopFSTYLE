<?php 
$pageTitle = "Cập nhật số lượng sản phẩm";

include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Kiểm tra GET parameter 'variant_id'
if (!isset($_GET['variant_id'])) {
    header("Location: index.php?msg=ID biến thể không hợp lệ.&type=failure");
    exit;
}

$variant_id = $_GET['variant_id'];
?>
<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Cập nhật số lượng</h1>
    <form method="POST" action="" class="bg-white shadow rounded p-6">
        <div class="mb-4">
            <label for="additionalQuantity" class="block mb-1 font-medium">
                Số lượng cần thêm:
            </label>
            <input type="number" name="additionalQuantity" id="additionalQuantity" min="1"
                class="w-full p-2 border border-gray-300 rounded" placeholder="Nhập số lượng cần thêm">
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded shadow transition">
                Cập nhật số lượng
            </button>
        </div>
    </form>
</main>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $additionalQuantity = $_POST['additionalQuantity'] ?? 0;
    if (updateVariantQuantity($conn, $variant_id, $additionalQuantity)) {
        header("Location: index.php?msg=Cập nhật số lượng thành công!&type=success");
        exit;
    } else {
        echo "<p class='text-red-500'>Cập nhật số lượng thất bại. Vui lòng thử lại.</p>";
    }
}
include('../../includes/footer.php');
ob_end_flush();
?>