<?php
include('../../includes/header.php');
include('../../includes/search.php');
require_once '../../includes/db.php';
require_once '../../model/ProductModel.php';

// Lấy product_id từ URL
$product_id = $_GET['id'] ?? '';
if (!$product_id) {
    die('Sản phẩm không hợp lệ.');
}

// Khởi tạo model và lấy dữ liệu
$model = new ProductModel($conn);
$product = $model->getProductDetail($product_id);
if (!$product) {
    die('Không tìm thấy sản phẩm hoặc đã ngừng kinh doanh.');
}
$total_stock = $model->getProductStock($product_id);
$variants = $model->getProductVariants($product_id);
$variant_json = json_encode($variants);

// Tính giá & khuyến mãi
$_orig = $product['original_price'] ?? 0;
$_disc = $product['discount_price'] ?? 0;
$hasDiscount = $_disc > 0 && $_disc < $_orig;
$discountPercent = $hasDiscount ? round(100 - ($_disc / $_orig) * 100) : 0;

// Rating mẫu
$rating = $product['rating'] ?? 4.5;
$starsFull = floor($rating);
$starsEmpty = 5 - $starsFull;
?>
<style>
.crossed-out {
    position: relative;
    opacity: 0.5;
    pointer-events: none;
}

.crossed-out::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: red;
    transform: rotate(-45deg);
    transform-origin: center;
}
</style>

<div class="container mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Ảnh sản phẩm -->
        <div class="flex justify-center">
            <img src="../../<?php echo htmlspecialchars($product['main_image']); ?>"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                class="w-full h-auto rounded-lg shadow-md max-w-md">
        </div>

        <!-- Thông tin chi tiết -->
        <div>
            <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="text-xl font-semibold text-red-500 mb-4">
                <?php if ($hasDiscount): ?>
                <span class="text-lg line-through text-gray-500"><?php echo number_format($_orig); ?> VND</span>
                <?php echo number_format($_disc); ?> VND
                <span
                    class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-md">-<?php echo $discountPercent; ?>%</span>
                <?php else: ?>
                <?php echo number_format($_orig); ?> VND
                <?php endif; ?>
            </p>

            <p class="mb-4">
                <?php for ($i = 0; $i < $starsFull; $i++): ?><span
                    class="text-yellow-500 text-lg">★</span><?php endfor; ?>
                <?php for ($i = 0; $i < $starsEmpty; $i++): ?><span
                    class="text-gray-300 text-lg">★</span><?php endfor; ?>
                <span class="text-sm text-gray-600">(<?php echo $rating; ?>)</span>
            </p>

            <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <p class="text-sm text-gray-600 mb-2">Ngày tạo:
                <?php echo date('d-m-Y', strtotime($product['created_at'])); ?></p>
            <p class="text-sm text-gray-600 mb-4">Trạng thái:
                <?php echo $product['status'] == 1 ? 'Còn bán' : 'Ngừng bán'; ?></p>
            <p class="text-sm text-gray-600 mb-4">Tồn kho tổng: <?php echo $total_stock; ?></p>

            <?php if (!empty($variants)): ?>
            <div class="mb-6">
                <!-- Chọn màu -->
                <p class="font-medium mb-2">Chọn màu:</p>
                <div class="flex gap-2" id="colorOptions">
                    <?php
                    $colors = [];
                    foreach ($variants as $v) {
                        if (!isset($colors[$v['color_id']])) {
                            $colors[$v['color_id']] = ['name' => $v['color_name'], 'code' => $v['color_code']];
                        }
                    }
                    foreach ($colors as $cid => $c): ?>
                    <button id="color-<?php echo $cid; ?>" type="button"
                        class="w-8 h-8 rounded-full border focus:outline-none"
                        style="background-color: <?php echo htmlspecialchars($c['code']); ?>;"
                        title="<?php echo htmlspecialchars($c['name']); ?>"
                        onclick="selectColor('<?php echo $cid; ?>')"></button>
                    <?php endforeach; ?>
                </div>

                <!-- Chọn kích cỡ -->
                <p class="font-medium mt-4 mb-2">Chọn kích cỡ:</p>
                <div id="sizeOptions" class="flex gap-2"></div>
            </div>
            <?php endif; ?>

            <!-- Số lượng & Thêm giỏ -->
            <div class="mb-6">
                <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Số lượng</label>
                <input id="quantity" type="number" min="1" value="1" class="border p-2 rounded-lg w-24" />
                <a href="javascript:addToCart('<?php echo $product['product_id']; ?>')"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ml-4">Thêm vào
                    giỏ</a>
            </div>

            <a href="javascript:history.back()" class="inline-block px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
                Quay lại</a>
        </div>
    </div>
</div>

<script>
const variants = <?php echo $variant_json; ?>;
let selectedColor = '';
let selectedSize = '';
const sizeOrder = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

function selectColor(cid) {
    selectedColor = cid;
    selectedSize = '';

    document.querySelectorAll('#colorOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    const colorBtn = document.getElementById('color-' + cid);
    colorBtn.classList.add('ring-2', 'ring-blue-500');

    updateSizeOptions(cid);
}

function updateSizeOptions(cid) {
    const container = document.getElementById('sizeOptions');
    container.innerHTML = '';

    const sizes = variants.filter(v => v.color_id === cid);

    // Sắp xếp theo thứ tự
    sizes.sort((a, b) => {
        return sizeOrder.indexOf(a.size_name) - sizeOrder.indexOf(b.size_name);
    });

    sizes.forEach(v => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.id = 'size-' + v.size_id;
        btn.textContent = v.size_name;
        btn.className = 'w-12 h-10 rounded-full border flex items-center justify-center text-sm';

        if (parseInt(v.quantity) === 0) {
            btn.classList.add('crossed-out', 'bg-gray-100', 'text-gray-400');
        } else {
            btn.classList.add('hover:bg-blue-50');
            btn.onclick = () => selectSize(v.size_id);
        }

        container.appendChild(btn);
    });
}

function selectSize(sid) {
    selectedSize = sid;
    document.querySelectorAll('#sizeOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    const sizeBtn = document.getElementById('size-' + sid);
    if (sizeBtn) sizeBtn.classList.add('ring-2', 'ring-blue-500');
}

function isColorOutOfStock(cid) {
    return !variants.some(v => v.color_id === cid && parseInt(v.quantity) > 0);
}

// Gạch chéo màu hết hàng + auto chọn màu đầu tiên còn hàng
window.addEventListener('DOMContentLoaded', () => {
    const uniqueColors = [...new Set(variants.map(v => v.color_id))];

    uniqueColors.forEach(cid => {
        if (isColorOutOfStock(cid)) {
            const colorBtn = document.getElementById('color-' + cid);
            if (colorBtn) colorBtn.classList.add('crossed-out');
        }
    });

    const availableColor = uniqueColors.find(cid => !isColorOutOfStock(cid));
    if (availableColor) {
        selectColor(availableColor);
    }
});
</script>


<?php 
// include('../../includes/footer.php'); 
?>