<?php
session_start();
include('../../includes/header.php');
include('../../includes/search.php');
require_once '../../includes/db.php';
require_once '../../model/FavouriteModel.php';
require_once '../../model/ProductModel.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/login.php');
    exit();
}

$customer_id = $_SESSION['customer']['customer_id'];
$favouriteModel = new FavouriteModel($conn);
$productModel = new ProductModel($conn);

// Lấy danh sách sản phẩm yêu thích
$favourites = $favouriteModel->getFavourites($customer_id);
?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-8">Sản phẩm yêu thích</h1>

    <?php if ($favourites->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php while ($product = $favourites->fetch_assoc()): 
                $hasDiscount = $product['discount_price'] > 0 && $product['discount_price'] < $product['original_price'];
                $discountPercent = $hasDiscount ? round(100 - ($product['discount_price'] / $product['original_price']) * 100) : 0;
            ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <a href="<?= USER_URL ?>/views/product_detail.php?id=<?= $product['product_id'] ?>">
                <img src="<?= USER_URL ?>/<?= htmlspecialchars($product['main_image']) ?>"
                    alt="<?= htmlspecialchars($product['product_name']) ?>" class="w-full h-64 object-cover">
            </a>
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">
                    <a href="<?= USER_URL ?>/views/product_detail.php?id=<?= $product['product_id'] ?>"
                        class="hover:text-blue-600">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </a>
                </h3>

                <div class="flex items-center gap-2 mb-4">
                    <?php if ($hasDiscount): ?>
                    <span class="text-lg font-bold text-red-600">
                        <?= number_format($product['discount_price']) ?>₫
                    </span>
                    <span class="text-sm text-gray-500 line-through">
                        <?= number_format($product['original_price']) ?>₫
                    </span>
                    <span class="text-sm bg-red-100 text-red-800 px-2 py-1 rounded">
                        -<?= $discountPercent ?>%
                    </span>
                    <?php else: ?>
                    <span class="text-lg font-bold text-gray-900">
                        <?= number_format($product['original_price']) ?>₫
                    </span>
                    <?php endif; ?>
                </div>

                <div class="flex justify-between items-center">
                    <button onclick="removeFavourite('<?= $product['product_id'] ?>')"
                        class="text-red-600 hover:text-red-800 font-medium">
                        Xóa khỏi yêu thích
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">Bạn chưa có sản phẩm yêu thích nào</p>
        <a href="<?= USER_URL ?>/views/product.php" class="text-blue-600 hover:text-blue-800 font-medium">
            Xem sản phẩm
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
async function removeFavourite(productId) {
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);

        const res = await fetch('<?= USER_URL ?>/controller/favouriteController.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            // Reload trang để cập nhật danh sách
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    } catch (err) {
        console.error(err);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    }
}
</script>

<?php include('../../includes/footer.php'); ?>