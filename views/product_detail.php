<?php
session_start();
include('../includes/header.php');
include('../includes/search.php');
require_once '../includes/db.php';
require_once '../model/productModel.php';
require_once '../model/favouriteModel.php';

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
$productImages = $model->getProductImagesArray($conn, $product_id);

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

#toast {
    z-index: 9999;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;

    /* Tăng padding và font-size để toast to hơn */
    padding: 1rem 1.5rem;
    font-size: 1rem;

    /* Bo góc và nền tối nhẹ */
    background: rgba(0, 0, 0, 0.85);
    color: #fff;
    border-radius: 0.5rem;

    /* Giãn cách so với phần tử phía trên */
    margin-top: 1.5rem;

    /* Giữ vị trí cũ với translateY */
    transform: translateY(20px);
}

#toast.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}
</style>
<!-- Toast Notification -->
<div id="toast"
    class="hidden fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg transition-opacity duration-300 opacity-0">
    <span id="toastMessage">Sản phẩm đã được thêm vào giỏ hàng!</span>
</div>
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
        <!-- Hình ảnh sản phẩm -->
        <!-- Hình ảnh sản phẩm -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Hình ảnh sản phẩm</h2>

            <!-- Bọc chung ảnh nhỏ và ảnh lớn -->
            <div class="flex gap-6">
                <!-- Danh sách ảnh nhỏ (nằm dọc) -->
                <div
                    class="flex flex-col gap-4 overflow-y-auto max-h-[700px] custom-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                    <?php foreach ($productImages as $index => $img): ?>
                    <img src="../admin/uploads/products/<?= htmlspecialchars($img['image_url']) ?>" alt="Ảnh sản phẩm"
                        class="w-24 h-24 object-cover rounded-lg cursor-pointer flex-shrink-0"
                        onclick="showPreview(this)" />
                    <?php endforeach; ?>
                </div>

                <!-- Ảnh xem trước -->
                <div class="relative w-full max-w-6xl h-[700px] rounded-lg overflow-hidden shadow-md bg-white">
                    <img id="mainPreview"
                        src="../admin/uploads/products/<?= htmlspecialchars($productImages[0]['image_url']) ?>"
                        alt="Ảnh xem trước" class="w-full h-full object-cover" />

                    <!-- Nút điều hướng dưới góc phải -->
                    <div class="absolute bottom-4 right-4 flex gap-2">
                        <button
                            class="bg-white text-gray-700 p-3 rounded-full shadow-md hover:bg-gray-100 flex items-center justify-center transition-transform hover:scale-110"
                            onclick="prevImage()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="w-5 h-5">
                                <path
                                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z" />
                            </svg>
                        </button>
                        <button
                            class="bg-white text-gray-700 p-3 rounded-full shadow-md hover:bg-gray-100 flex items-center justify-center transition-transform hover:scale-110"
                            onclick="nextImage()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="w-5 h-5">
                                <path
                                    d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>






        <!-- Ảnh sản phẩm phụ -->
        <?php if (!empty($images)): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 justify-center">
            <?php foreach ($images as $image): ?>
            <div class="flex justify-center">
                <img src="../<?= htmlspecialchars($image['image_url']); ?>"
                    alt="<?= htmlspecialchars($product['product_name']); ?> - Ảnh phụ"
                    class="w-full max-w-[150px] h-auto rounded-xl shadow-md transition-transform hover:scale-105 duration-300 cursor-pointer">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>


        <!-- Thông tin sản phẩm -->
        <div>
            <h1 class="text-4xl font-extrabold mb-4 text-gray-800"><?= htmlspecialchars($product['product_name']); ?>
            </h1>

            <!-- Giá -->
            <p class="text-2xl font-bold text-blue-600 mb-6 flex items-center gap-2">
                <?php if ($hasDiscount): ?>
                <span class="text-lg line-through text-gray-400"><?= number_format($_orig); ?>₫</span>
                <?= number_format($_disc); ?>₫
                <span
                    class="bg-blue-200 text-blue-800 text-sm font-semibold px-2 py-0.5 rounded-full">-<?= $discountPercent; ?>%</span>
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
                <!-- Chọn kích cỡ -->
                <p class="font-medium text-gray-700 mb-2">Chọn kích cỡ:</p>
                <div id="sizeOptions" class="flex flex-wrap mb-4 space-x-4 space-y-2"></div>

            </div>
            <?php endif; ?>

            <!-- Số lượng & Giỏ hàng & Yêu thích -->
            <div class="flex items-center gap-x-6 mb-8">
                <div class="flex items-center">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mr-2">Số lượng</label>
                    <input id="quantity" type="number" min="1" value="1" class="border-gray-300 rounded-lg p-2 w-24" />
                </div>

                <button id="addCartBtn" onclick="addToCart('<?= $product['product_id']; ?>')"
                    class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow hover:shadow-lg hover:scale-105 transform transition disabled:opacity-50"
                    disabled>Thêm vào giỏ</button>

                <!-- Nút yêu thích -->
                <button id="favBtn" data-favourited="<?= $isFav ? '1' : '0'; ?>"
                    class="group flex items-center justify-center w-12 h-12 rounded-full border-2 border-gray-300 bg-white text-gray-500 hover:scale-110 transition-all duration-300 ease-in-out"
                    title="<?= $isFav ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích'; ?>">
                    <svg id="favIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                        class="w-6 h-6 fill-current transition-colors duration-300 <?= $isFav ? 'text-red-500' : 'text-gray-400'; ?>">
                        <path
                            d="<?= $isFav ? 'M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z' 
        : 
        'M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z'; ?>" />
                    </svg>
                </button>



            </div>



            <!-- Mô tả sản phẩm (Đưa xuống dưới cùng) -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h2 class="text-xl font-semibold mb-2 text-gray-800">Mô tả sản phẩm</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                    <?= htmlspecialchars($product['description']); ?></p>
            </div>
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

function addToCart(variantId) {
    const quantity = qtyInput.value;
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
                showToast('Sản phẩm đã được thêm vào giỏ hàng!',
                    '<?= htmlspecialchars($product['product_name']); ?>');
                updateCartCount();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function showToast(message, productName) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = `${productName}: ${message}`;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000); // Ẩn toast sau 3 giây
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



<!-- Xử lý ảnh -->
<script>
let currentIndex = 0;

function showPreview(imgElement) {
    const preview = document.getElementById('mainPreview');
    preview.src = imgElement.src;
    currentIndex = [...imgElement.parentNode.children].indexOf(imgElement);
}

function nextImage() {
    const images = document.querySelectorAll('.custom-scroll img');
    currentIndex = (currentIndex + 1) % images.length;
    const nextImg = images[currentIndex];
    showPreview(nextImg);
}

function prevImage() {
    const images = document.querySelectorAll('.custom-scroll img');
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    const prevImg = images[currentIndex];
    showPreview(prevImg);
}
</script>

<!-- xử lý chọn size và màu -->
<script>
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
</script>

<!-- xử lý yêu thích -->
<script>
document.getElementById('favBtn').addEventListener('click', async function() {
    const btn = this;
    const icon = document.getElementById('favIcon');
    const isFav = btn.dataset.favourited == '1';
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

        if (data.success) {
            btn.dataset.favourited = isFav ? '0' : '1';
            btn.title = isFav ? 'Thêm vào yêu thích' : 'Xóa khỏi yêu thích';
            icon.setAttribute('d', isFav ?
                'M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z' :
                'M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z'
            );

            icon.classList.toggle('text-red-500', !isFav);
            icon.classList.toggle('text-gray-400', isFav);
        }
    } catch (err) {
        console.error(err);
    }
});
</script>