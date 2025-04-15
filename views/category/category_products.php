<?php 
    // Bao gồm các tệp cần thiết
    include ('../../includes/header.php'); 
    include ('../../includes/search.php');  

    // Kết nối cơ sở dữ liệu
    require_once '../../includes/db.php';
    require_once '../../model/productModel.php';  // Giả sử bạn có model xử lý sản phẩm

    $category_id = isset($_GET['id']) ? $_GET['id'] : '';

    
    if ($category_id <= 0) {
        echo "Danh mục không hợp lệ.";
        exit;
    }

    // Tạo đối tượng ProductModel và lấy sản phẩm theo category_id
    $productModel = new ProductModel($conn);
    $products = $productModel->getProductsByCategoryUUID($category_id);
?>

<!-- Nội dung danh sách sản phẩm -->
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Sản phẩm thuộc danh mục</h1>

    <!-- Kiểm tra nếu có sản phẩm -->
    <?php if (!empty($products)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $product): ?>
        <?php
        $original_price = $product['original_price'];
        $discount_price = $product['discount_price'];
        $has_discount = $discount_price && $discount_price < $original_price;
        $discount_percent = $has_discount ? round(100 - ($discount_price / $original_price) * 100) : 0;
    ?>
        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>"
            class="block border border-gray-200 rounded-xl shadow hover:shadow-lg transition duration-300 p-4 hover:scale-[1.02]">
            <div>
                <!-- Điều chỉnh kích thước ảnh -->
                <img src="../../<?php echo htmlspecialchars($product['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="w-full h-96 object-cover rounded-lg mb-4"> <!-- Tăng chiều cao của ảnh -->
                <h2 class="text-lg font-semibold text-gray-800 mb-1 line-clamp-2">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h2>

                <div class="text-base font-medium text-gray-900 flex items-center gap-2">
                    <?php if ($has_discount): ?>
                    <!-- Giá có giảm giá -->
                    <span class="text-red-600 text-xl"><?php echo number_format($discount_price); ?>đ</span>
                    <span
                        class="line-through text-sm text-gray-400"><?php echo number_format($original_price); ?>đ</span>
                    <span
                        class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-md"><?php echo "-$discount_percent%"; ?></span>
                    <?php else: ?>
                    <!-- Giá không giảm giá -->
                    <span class="text-xl"><?php echo number_format($original_price); ?>đ</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <p class="text-center">Không có sản phẩm nào trong danh mục này.</p>
    <?php endif; ?>
</div>



<?php 
    // Bao gồm phần chân trang
    include ('../../includes/footer.php'); 
?>