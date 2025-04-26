<?php
session_start();
include('../includes/header.php');
include('../includes/search.php');
require_once '../includes/db.php';
require_once '../model/ProductModel.php';
require_once '../model/FavouriteModel.php';

// Lấy product_id từ URL
$product_id = $_GET['id'] ?? '';
if (!$product_id) {
    die('Sản phẩm không hợp lệ.');
}

// Khởi tạo model và lấy dữ liệu sản phẩm
$model       = new ProductModel($conn);
$product     = $model->getProductDetail($product_id);
if (!$product) {
    die('Không tìm thấy sản phẩm hoặc đã ngừng kinh doanh.');
}
$total_stock   = $model->getProductStock($product_id);
$variants      = $model->getProductVariants($product_id);
$variant_json  = json_encode($variants);

// Kiểm tra trạng thái yêu thích
$isFav = false;
if (!empty($_SESSION['customer'])) {
    $favModel = new FavouriteModel($conn);
    $isFav    = $favModel->isFavourite($_SESSION['customer']['customer_id'], $product_id);
}

// Tính giá & khuyến mãi
$_orig           = $product['original_price']  ?? 0;
$_disc           = $product['discount_price']  ?? 0;
$hasDiscount     = $_disc > 0 && $_disc < $_orig;
$discountPercent = $hasDiscount ? round(100 - ($_disc / $_orig) * 100) : 0;

// Rating mẫu
$rating     = $product['rating'] ?? 4.5;
$starsFull  = floor($rating);
$starsEmpty = 5 - $starsFull;
?>

<style>
/* Giữ lại cross-out style nếu cần */
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

<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
        <!-- Ảnh sản phẩm -->
        <div class="flex justify-center">
            <img src="../<?= htmlspecialchars($product['main_image']); ?>"
                alt="<?= htmlspecialchars($product['product_name']); ?>"
                class="w-full max-w-md h-auto rounded-2xl shadow-lg transition-transform hover:scale-105 duration-300">
        </div>

        <!-- Thông tin sản phẩm -->
        <div>
            <h1 class="text-4xl font-extrabold mb-4 text-gray-800"><?= htmlspecialchars($product['product_name']); ?>
            </h1>

            <!-- Giá -->
            <p class="text-2xl font-bold text-red-600 mb-6 flex items-center gap-2">
                <?php if ($hasDiscount): ?>
                <span class="text-lg line-through text-gray-400"><?= number_format($_orig); ?>₫</span>
                <?= number_format($_disc); ?>₫
                <span
                    class="bg-red-200 text-red-800 text-sm font-semibold px-2 py-0.5 rounded-full">-<?= $discountPercent; ?>%</span>
                <?php else: ?>
                <?= number_format($_orig); ?>₫
                <?php endif; ?>
            </p>

            <!-- Đánh giá -->
            <p class="mb-6 flex items-center gap-1">
                <?php for ($i = 0; $i < $starsFull; $i++): ?><span
                    class="text-yellow-400 text-xl">★</span><?php endfor; ?>
                <?php for ($i = 0; $i < $starsEmpty; $i++): ?><span
                    class="text-gray-300 text-xl">★</span><?php endfor; ?>
                <span class="text-sm text-gray-500 ml-2">(<?= $rating; ?>)</span>
            </p>

            <!-- Thông tin thêm -->
            <p class="text-sm text-gray-500 mb-2">Ngày tạo: <?= date('d-m-Y', strtotime($product['created_at'])); ?></p>
            <p class="text-sm text-gray-500 mb-4">Trạng thái: <?= $product['status'] == 1 ? 'Còn bán' : 'Ngừng bán'; ?>
            </p>
            <p class="text-sm mb-6">
                <span class="text-gray-600">Tồn kho:</span>
                <span id="stockDisplay" class="font-medium text-gray-900"><?= $total_stock; ?></span>
            </p>

            <!-- Biến thể sản phẩm -->
            <?php if (!empty($variants)): ?>
            <div class="mb-8">
                <!-- Chọn màu -->
                <p class="font-medium text-gray-700 mb-2">Chọn màu:</p>
                <div class="flex gap-3 flex-wrap mb-4" id="colorOptions">
                    <?php
                    $colors = [];
                    foreach ($variants as $v) {
                        if (!isset($colors[$v['color_id']])) {
                            $colors[$v['color_id']] = ['name'=>$v['color_name'],'code'=>$v['color_code']];
                        }
                    }
                    foreach ($colors as $cid => $c): ?>
                    <button id="color-<?= $cid; ?>" type="button"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 hover:ring-2 hover:ring-offset-1 hover:ring-blue-500 transition"
                        style="background-color: <?= htmlspecialchars($c['code']); ?>;"
                        title="<?= htmlspecialchars($c['name']); ?>" onclick="selectColor('<?= $cid; ?>')">
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Chọn kích cỡ -->
                <p class="font-medium text-gray-700 mb-2">Chọn kích cỡ:</p>
                <div id="sizeOptions" class="flex gap-3 flex-wrap"></div>
            </div>
            <?php endif; ?>

            <!-- Số lượng & Giỏ hàng & Yêu thích -->
            <div class="flex flex-wrap items-center gap-6 mb-8">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                    <input id="quantity" type="number" min="1" value="1"
                        class="border-gray-300 rounded-lg p-2 w-24 disabled:opacity-50" disabled />
                </div>
                <button id="addCartBtn" onclick="addToCart('<?= $product['product_id']; ?>')"
                    class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow hover:shadow-lg hover:scale-105 transform transition disabled:opacity-50"
                    disabled>Thêm vào giỏ</button>

                <!-- Nút yêu thích -->
                <button id="favBtn" data-favourited="<?= $isFav ? '1':'0'; ?>"
                    class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow hover:shadow-lg hover:scale-105 transform transition disabled:opacity-50"
                    title="<?= $isFav ? 'Xóa khỏi yêu thích':'Thêm vào yêu thích'; ?>">
                    <?php if ($isFav): ?>
                        Xóa khỏi yêu thích
                    <?php else: ?>
                        Thêm vào yêu thích
                    <?php endif; ?>
                </button>
            </div>

            <!-- Mô tả sản phẩm (Đưa xuống dưới cùng) -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h2 class="text-xl font-semibold mb-2 text-gray-800">Mô tả sản phẩm</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                    <?= htmlspecialchars($product['description']); ?></p>
            </div>

            <!-- Nút quay lại -->
            <a href="javascript:history.back()"
                class="inline-block mt-6 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium shadow hover:shadow-md transition">←
                Quay lại</a>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
const variants = <?= $variant_json; ?>;
const totalStock = <?= $total_stock; ?>;
let selectedColor = '';
let selectedSize = '';
const sizeOrder = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
const stockEl = document.getElementById('stockDisplay');
const qtyInput = document.getElementById('quantity');
const addBtn = document.getElementById('addCartBtn');
const favBtn = document.getElementById('favBtn');
const sizeOptionsEl = document.getElementById('sizeOptions');

// Chọn màu và cập nhật thông tin
function selectColor(cid) {
    selectedColor = cid;
    selectedSize = ''; // Đặt lại kích thước khi thay đổi màu

    // Xóa lớp lựa chọn màu cũ
    document.querySelectorAll('#colorOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    document.getElementById('color-' + cid)?.classList.add('ring-2', 'ring-blue-500');

    // Cập nhật các tùy chọn kích thước
    updateSizeOptions(cid);
    sessionStorage.setItem('selectedColor', cid);
    console.log(sessionStorage.getItem('selectedColor'));
    // Hiển thị lại số lượng tồn kho và thiết lập giá trị mặc định cho số lượng
    stockEl.textContent = totalStock;
    qtyInput.value = 1;
    qtyInput.max = totalStock;
    addBtn.disabled = true; // Vô hiệu nút thêm vào giỏ hàng nếu chưa chọn kích thước
}

// Cập nhật các tùy chọn kích thước khi chọn màu
function updateSizeOptions(cid) {
    sizeOptionsEl.innerHTML = ''; // Xóa các tùy chọn kích thước cũ
    
    // Lọc các biến thể có size cho màu đã chọn
    const sizes = variants.filter(v => v.color_id === cid && v.size_name !== null);

    if (sizes.length === 0) {
        sizeOptionsEl.style.display = 'none'; // Ẩn phần chọn kích thước nếu không có size
        addBtn.disabled = false; // Nếu không có size, cho phép thêm vào giỏ hàng
    } else {
        sizeOptionsEl.style.display = 'block'; // Hiển thị lại phần chọn kích thước
        sizes.sort((a, b) => sizeOrder.indexOf(a.size_name) - sizeOrder.indexOf(b.size_name));
        sizes.forEach(v => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = 'size-' + v.size_id;
            btn.textContent = v.size_name;
            btn.className = 'w-12 h-10 rounded-lg border text-sm font-semibold shadow-sm transition';
            if (parseInt(v.quantity) === 0) {
                btn.classList.add('crossed-out', 'bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
            } else {
                btn.classList.add('hover:bg-blue-50');
                btn.onclick = () => selectSize(v.size_id);
            }
            sizeOptionsEl.appendChild(btn);
        });
    }
}

// Chọn kích thước và cập nhật thông tin số lượng
function selectSize(sid) {
    selectedSize = sid;
    document.querySelectorAll('#sizeOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    document.getElementById('size-' + sid)?.classList.add('ring-2', 'ring-blue-500');
    sessionStorage.setItem('selectedSize', sid);
    console.log(sessionStorage.getItem('selectedSize'));
    const variant = variants.find(v => v.color_id === selectedColor && v.size_id === selectedSize);
    if (variant) {
        const q = parseInt(variant.quantity);
        stockEl.textContent = q;
        qtyInput.max = q;
        qtyInput.value = Math.min(q, qtyInput.value);
        addBtn.disabled = (q === 0);
    }
}


// Xử lý yêu thích
favBtn.addEventListener('click', async function() {
    const isFav = this.dataset.favourited == '1';
    const action = isFav ? 'remove' : 'add';
    
    try {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', '<?= $product_id; ?>');

        const res = await fetch('<?= USER_URL ?>/controller/favouriteController.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        
        if (!data.success) {
            if (data.message === 'Vui lòng đăng nhập') {
                alert('Bạn cần đăng nhập để tiếp tục');
                window.location.href = '<?= USER_URL ?>/login.php';
                return;
            }
            alert(data.message || 'Có lỗi xảy ra');
            return;
        }

        // Cập nhật trạng thái nút
        this.dataset.favourited = isFav ? '0' : '1';
        this.innerHTML = isFav ? 
            'Thêm vào yêu thích' : 
            'Xóa khỏi yêu thích';
        this.title = isFav ? 'Thêm vào yêu thích' : 'Xóa khỏi yêu thích';
        alert(data.message);
    } catch (err) {
        console.error(err);
    }
});

// Xử lý khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    const uniqueColors = [...new Set(variants.map(v => v.color_id))];
    uniqueColors.forEach(cid => {
        if (!variants.some(v => v.color_id === cid && parseInt(v.quantity) > 0)) {
            document.getElementById('color-' + cid)?.classList.add('crossed-out');
        }
    });
    const available = uniqueColors.find(cid => variants.some(v => v.color_id === cid && parseInt(v.quantity) >
        0));
    if (available) selectColor(available);
});

function addToCart(variantId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('size_id', sessionStorage.getItem('selectedSize'));
    formData.append('color_id', sessionStorage.getItem('selectedColor'));
    formData.append('product_id', '<?= $product_id; ?>');
    formData.append('quantity', quantity);

    fetch('<?= USER_URL ?>/controller/cartController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            alert('Đã thêm vào giỏ hàng!');
            updateCartCount();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại!');
    });
}

function updateCartCount() {
    fetch('<?= USER_URL ?>/controller/cartController.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.data.items.length;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>