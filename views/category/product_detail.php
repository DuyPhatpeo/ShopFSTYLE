<?php
include('../../includes/header.php');
include('../../includes/search.php');

require_once '../../includes/db.php';
require_once '../../model/productModel.php';

// Lấy product_id (UUID) từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($product_id)) {
    echo "Sản phẩm không hợp lệ.";
    exit;
}

// Khởi tạo ProductModel
$productModel = new ProductModel($conn);

// 1. Lấy chi tiết sản phẩm
$product = $productModel->getProductDetail($product_id);
if (!$product) {
    echo "Không tìm thấy sản phẩm hoặc sản phẩm đã ngừng kinh doanh.";
    exit;
}

// 2. Tính tồn kho tổng
$total_stock = $productModel->getProductStock($product_id);

// 3. Lấy danh sách các biến thể (bao gồm thông tin: color_id, color_name, color_code, size_id, size_name, quantity)
$variants = $productModel->getProductVariants($product_id);

// Tính giá và khuyến mãi
$original_price = $product['original_price'] ?? 0;
$discount_price = $product['discount_price'] ?? 0;
$has_discount = ($discount_price > 0 && $discount_price < $original_price);
$discount_percent = $has_discount ? round(100 - ($discount_price / $original_price) * 100) : 0;

// Giả sử giá trị rating được lưu trong product (hoặc bạn có thể tính trung bình từ bảng review)
$rating = $product['rating'] ?? 4.5;
$starFull = floor($rating);
$starEmpty = 5 - $starFull;
?>

<div class="container mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Phần hình ảnh sản phẩm -->
        <div class="flex justify-center">
            <img src="../../<?php echo htmlspecialchars($product['main_image']); ?>"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                class="w-full h-auto rounded-lg shadow-md max-w-md">
        </div>

        <!-- Thông tin sản phẩm -->
        <div>
            <!-- Tên sản phẩm -->
            <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h1>

            <!-- Thông tin giá -->
            <p class="text-xl font-semibold text-red-500 mb-4">
                <?php if ($has_discount): ?>
                <span class="text-lg line-through text-gray-500">
                    <?php echo number_format($original_price); ?> VND
                </span>
                <?php echo number_format($discount_price); ?> VND
                <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-md">
                    <?php echo "-$discount_percent%"; ?>
                </span>
                <?php else: ?>
                <?php echo number_format($original_price); ?> VND
                <?php endif; ?>
            </p>

            <!-- Hiển thị đánh giá -->
            <p class="mb-4">
                <?php for ($i = 0; $i < $starFull; $i++): ?>
                <span class="text-yellow-500 text-lg">★</span>
                <?php endfor; ?>
                <?php for ($i = 0; $i < $starEmpty; $i++): ?>
                <span class="text-gray-300 text-lg">★</span>
                <?php endfor; ?>
                <span class="text-sm text-gray-600">(<?php echo $rating; ?>)</span>
            </p>

            <!-- Mô tả sản phẩm -->
            <p class="text-gray-700 mb-6">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <!-- Thông tin khác -->
            <p class="text-sm text-gray-600 mb-2">
                Ngày tạo: <?php echo date("d-m-Y", strtotime($product['created_at'])); ?>
            </p>
            <p class="text-sm text-gray-600 mb-4">
                Trạng thái: <?php echo ($product['status'] == 1) ? "Còn bán" : "Ngừng bán"; ?>
            </p>
            <p class="text-sm text-gray-600 mb-4">
                Tồn kho tổng (tất cả biến thể): <?php echo $total_stock; ?>
            </p>

            <!-- Hiển thị biến thể sản phẩm: chọn màu và kích cỡ -->
            <?php if (!empty($variants)): ?>
            <div class="mb-6">
                <p class="font-medium mb-2">Chọn màu:</p>
                <div class="flex gap-2">
                    <?php 
                        // Tạo mảng các màu không trùng lặp dựa trên color_id
                        $colors = [];
                        foreach ($variants as $variant) {
                            if (!isset($colors[$variant['color_id']])) {
                                $colors[$variant['color_id']] = [
                                    'color_name' => $variant['color_name'],
                                    'color_code' => $variant['color_code']
                                ];
                            }
                        }
                        foreach ($colors as $color_id => $color) :
                    ?>
                    <button type="button" class="w-8 h-8 rounded-full border border-gray-300 focus:outline-none"
                        style="background-color: <?php echo htmlspecialchars($color['color_code']); ?>;"
                        onclick="selectColor('<?php echo htmlspecialchars($color_id); ?>')"
                        id="color-<?php echo htmlspecialchars($color_id); ?>"
                        title="<?php echo htmlspecialchars($color['color_name']); ?>">
                    </button>
                    <?php endforeach; ?>
                </div>
                <p class="font-medium mt-4 mb-2">Chọn kích cỡ:</p>
                <div class="flex gap-2" id="sizeOptions">
                    <?php 
                        // Giả sử các kích cỡ của sản phẩm được lấy từ biến thể của màu đầu tiên (mặc định)
                        $default_color_id = array_key_first($colors);
                        foreach ($variants as $variant) {
                            if ($variant['color_id'] == $default_color_id):
                    ?>
                    <button type="button" class="px-3 py-1 border rounded focus:outline-none"
                        onclick="selectSize('<?php echo htmlspecialchars($variant['size_id']); ?>')"
                        id="size-<?php echo htmlspecialchars($variant['size_id']); ?>">
                        <?php echo htmlspecialchars($variant['size_name']); ?>
                    </button>
                    <?php 
                            endif;
                        }
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Số lượng và nút thêm vào giỏ -->
            <div class="mb-6">
                <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Số lượng</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1"
                    class="border border-gray-300 p-2 rounded-lg w-24" />

                <a href="javascript:void(0)"
                    onclick="addToCart('<?php echo htmlspecialchars($product['product_id']); ?>')"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ml-4">
                    Thêm vào giỏ
                </a>
            </div>

            <a href="javascript:history.back()"
                class="inline-block px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                ← Quay lại
            </a>
        </div>
    </div>
</div>

<script>
// Biến global để lưu trạng thái chọn màu và kích cỡ
let selectedColor = '<?php echo $default_color_id ?? ''; ?>';
let selectedSize = '';

// Hàm xử lý chọn màu
function selectColor(colorId) {
    selectedColor = colorId;
    // Highlight màu được chọn
    document.querySelectorAll('[id^="color-"]').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500');
    });
    document.getElementById('color-' + colorId).classList.add('ring-2', 'ring-blue-500');

    // Cập nhật các tùy chọn kích cỡ theo màu được chọn
    updateSizeOptions();
}

// Hàm cập nhật kích cỡ dựa theo màu được chọn
function updateSizeOptions() {
    // Ở đây bạn có thể gọi AJAX để lấy danh sách kích cỡ theo màu (nếu cần) 
    // hoặc lọc lại từ một biến JavaScript được render sẵn.
    // Ví dụ đơn giản: Lấy tất cả các option kích cỡ có sẵn từ một mảng JSON (nếu bạn render biến thể trên trang)
    // Nếu không có AJAX, bạn có thể refresh lại toàn bộ option từ chính biến thể đã render sẵn.
    // Ở ví dụ này, mình cho rằng các kích cỡ của màu mặc định (selectedColor) được render sẵn trong HTML.
    // Bạn có thể ẩn hiện các nút kích cỡ theo màu nếu như muốn.
    // Ví dụ:
    document.querySelectorAll('#sizeOptions button').forEach(btn => {
        // Nếu nút button có id chứa màu (bạn có thể thêm data attribute khi render lại list size), thì hiển thị
        // Ở đây đơn giản không thay đổi gì, nhưng bạn có thể chỉnh sửa theo yêu cầu.
        btn.style.display = 'inline-block';
    });
}

// Hàm chọn kích cỡ
function selectSize(sizeId) {
    selectedSize = sizeId;
    // Highlight kích cỡ được chọn
    document.querySelectorAll('[id^="size-"]').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500');
    });
    document.getElementById('size-' + sizeId).classList.add('ring-2', 'ring-blue-500');
}

// Hàm thêm sản phẩm vào giỏ hàng (giả định sử dụng localStorage)
function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);

    // Nếu có chọn biến thể, bạn có thể lưu thêm thông tin color và size
    const product = {
        id: productId,
        quantity: quantity,
        color: selectedColor,
        size: selectedSize
    };

    // Lấy giỏ hàng hiện tại từ localStorage hoặc tạo mảng rỗng
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingProductIndex = cart.findIndex(item => item.id === productId && item.color === selectedColor && item
        .size === selectedSize);

    if (existingProductIndex !== -1) {
        // Nếu sản phẩm đã có trong giỏ với biến thể đã chọn, cập nhật số lượng
        cart[existingProductIndex].quantity += quantity;
    } else {
        // Nếu chưa có, thêm mới sản phẩm vào giỏ
        cart.push(product);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert("Sản phẩm đã được thêm vào giỏ hàng!");
}
</script>

<?php include('../../includes/footer.php'); ?>