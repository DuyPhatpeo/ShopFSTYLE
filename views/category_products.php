<?php 
    // Bao gồm các tệp cần thiết
    include ('../includes/header.php'); 
    require_once '../includes/db.php';
    require_once '../model/productModel.php';

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

    <?php if (!empty($products)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $product): ?>
        <?php
            // Lấy ảnh chính của sản phẩm
            $image_url = $productModel->getMainProductImage($conn, $product['product_id']);
            $image_url = $image_url ? $image_url : 'default-image.jpg'; // Nếu không có ảnh chính, dùng ảnh mặc định

            // Các thông tin về giá cả
            $original_price = $product['original_price'];
            $discount_price = $product['discount_price'];
            $has_discount = $discount_price && $discount_price < $original_price;
            $discount_percent = $has_discount ? round(100 - ($discount_price / $original_price) * 100) : 0;
        ?>
        <!-- Card sản phẩm -->
        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>"
            class="block rounded-xl transition duration-300 p-4 relative group">
            <div class="relative">
                <!-- Hình sản phẩm -->
                <img src="../admin/uploads/products/<?php echo htmlspecialchars($image_url); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="w-full h-96 object-cover rounded-lg">
                <!-- Overlay nút thêm giỏ -->
                <div
                    class="absolute inset-x-0 bottom-0 flex justify-end pr-2 pb-2 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition duration-300 rounded-b-lg">
                    <button onclick="event.stopPropagation(); addToCart('<?php echo $product['product_id']; ?>')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded-md shadow-lg transition duration-300 transform hover:scale-105">
                        <i class="fas fa-cart-plus mr-1"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
            <!-- Thông tin sản phẩm -->
            <h2 class="text-lg font-semibold text-gray-800 mt-2 line-clamp-2">
                <?php echo htmlspecialchars($product['product_name']); ?>
            </h2>
            <div class="text-base font-medium text-gray-900 flex items-center gap-2 mt-1">
                <?php if ($has_discount): ?>
                <span class="text-blue-600 text-xl"><?php echo number_format($discount_price); ?>đ</span>
                <span class="line-through text-sm text-gray-400"><?php echo number_format($original_price); ?>đ</span>
                <span
                    class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-md"><?php echo "-$discount_percent%"; ?></span>
                <?php else: ?>
                <span class="text-xl"><?php echo number_format($original_price); ?>đ</span>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center">Không có sản phẩm nào trong danh mục này.</p>
    <?php endif; ?>
</div>

<script>
function addToCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const product = {
        id: productId,
        quantity: 1
    };
    const index = cart.findIndex(item => item.id === productId);
    if (index > -1) {
        cart[index].quantity += 1;
    } else {
        cart.push(product);
    }
    localStorage.setItem('cart', JSON.stringify(cart));

    // Thông báo bằng toastr
    toastr.success('Đã thêm sản phẩm vào giỏ hàng!', 'Thành công', {
        closeButton: true,
        progressBar: true,
        timeOut: 2000,
        positionClass: "toast-bottom-right"
    });
}
</script>

<?php 
    // Bao gồm phần chân trang
    include ('../includes/footer.php'); 
?>