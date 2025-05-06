<?php 
// Bao gồm header (đã có <html>, <head> với Tailwind CSS, mở <body>)
include ('../includes/header.php'); 
?>

<div class="min-h-screen flex flex-col">

    <!-- Nội dung chính -->
    <main class="flex-grow container mx-auto p-4">
        <?php
            require_once '../includes/db.php';
            require_once '../model/productModel.php';

            $category_id = isset($_GET['id']) ? $_GET['id'] : '';

            if ($category_id <= 0) {
                echo "<p class=\"text-center text-red-500\">Danh mục không hợp lệ.</p>";
                exit;
            }

            $productModel = new ProductModel($conn);
            $products = $productModel->getProductsByCategoryUUID($category_id);
        ?>

        <h1 class="text-2xl font-semibold mb-4">Sản phẩm thuộc danh mục</h1>

        <?php if (!empty($products)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <?php
                $image = $productModel->getMainProductImage($conn, $product['product_id']);
                $image_url = $image ? $image : 'default-image.jpg';

                $original = $product['original_price'];
                $discount = $product['discount_price'];
                $has_discount = $discount && $discount < $original;
                $percent = $has_discount ? round(100 - ($discount / $original) * 100) : 0;
            ?>
            <a href="product_detail.php?id=<?php echo htmlspecialchars($product['product_id']); ?>"
                class="block bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">
                <img src="../admin/uploads/products/<?php echo htmlspecialchars($image_url); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-64 object-cover">
                <div class="p-4">
                    <h2 class="text-lg font-semibold line-clamp-2 mb-2">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </h2>
                    <div class="flex items-center gap-2">
                        <?php if ($has_discount): ?>
                        <span class="text-blue-600 text-xl font-bold">
                            <?php echo number_format($discount); ?>đ
                        </span>
                        <span class="line-through text-gray-400">
                            <?php echo number_format($original); ?>đ
                        </span>
                        <span class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-md">
                            -<?php echo $percent; ?>%
                        </span>
                        <?php else: ?>
                        <span class="text-gray-900 text-xl font-bold">
                            <?php echo number_format($original); ?>đ
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-gray-500">Không có sản phẩm nào trong danh mục này.</p>
        <?php endif; ?>
    </main>
</div>
<!-- Footer -->
<?php include ('../includes/footer.php'); ?>