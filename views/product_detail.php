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

.toast-notification {
    position: fixed;
    top: 24px;
    right: 24px;
    width: 320px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    padding: 12px 16px;
    animation: slideIn 0.4s ease-out, fadeOut 0.4s ease-in 4.5s forwards;
    z-index: 10000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease;
}

.toast-content {
    display: flex;
    align-items: center;
    flex: 1;
}

.toast-image {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 14px;
    border: 1px solid #eee;
}

.toast-text p {
    margin: 0;
    font-size: 15px;
    color: #444;
    line-height: 1.4;
}

@keyframes slideIn {
    0% {
        opacity: 0;
        transform: translateX(120%);
    }

    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: translateX(120%);
        pointer-events: none;
    }
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
                <h1 class="text-4xl font-extrabold mb-4 text-gray-800">
                    <?= htmlspecialchars($product['product_name']); ?>
                </h1>

                <!-- Giá -->
                <p class="text-2xl font-bold text-red-600 mb-6 flex items-center gap-2">
                    <?php if ($hasDiscount): ?>
                    <span class="text-lg line-through text-gray-400"><?= number_format($_orig); ?>₫</span>
                    <?= number_format($_disc); ?>₫
                    <span class="bg-red-200 text-red-800 text-sm font-semibold px-2 py-0.5 rounded-full">
                        -<?= $discountPercent; ?>%
                    </span>
                    <?php else: ?>
                    <?= number_format($_orig); ?>₫
                    <?php endif; ?>
                </p>

                <!-- Đánh giá -->
                <p class="mb-6 flex items-center gap-1">
                    <?php for ($i = 0; $i < $starsFull;  $i++): ?><span
                        class="text-yellow-400 text-xl">★</span><?php endfor; ?>
                    <?php for ($i = 0; $i < $starsEmpty; $i++): ?><span
                        class="text-gray-300 text-xl">★</span><?php endfor; ?>
                    <span class="text-sm text-gray-500 ml-2">(<?= $rating; ?>)</span>
                </p>

                <!-- Thông tin thêm -->
                <p class="text-sm text-gray-500 mb-2">
                    Ngày tạo: <?= date('d-m-Y', strtotime($product['created_at'])); ?>
                </p>
                <p class="text-sm text-gray-500 mb-4">
                    Trạng thái: <?= $product['status'] == 1 ? 'Còn bán' : 'Ngừng bán'; ?>
                </p>
                <p class="text-sm mb-6">
                    <span class="text-gray-600">Tồn kho:</span>
                    <span id="stockDisplay" class="font-medium text-gray-900"><?= $total_stock; ?></span>
                </p>

                <!-- Biến thể sản phẩm -->
                <?php if (!empty($variants)): ?>
                <div class="mb-8">
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
                            class="w-10 h-10 rounded-full border-2 border-gray-300 transition"
                            style="background-color: <?= htmlspecialchars($c['code']); ?>;"
                            title="<?= htmlspecialchars($c['name']); ?>" onclick="selectColor('<?= $cid; ?>')">
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <p class="font-medium text-gray-700 mb-2">Chọn kích cỡ:</p>
                    <div id="sizeOptions" class="flex gap-3 flex-wrap"></div>
                </div>
                <?php endif; ?>

                <!-- Số lượng & Giỏ hàng & Yêu thích -->
                <div class="flex flex-wrap items-end gap-4 mb-8">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                        <input id="quantity" type="number" min="1" value="1"
                            class="border border-gray-300 rounded-lg p-2 w-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            max="<?= $total_stock; ?>" />
                    </div>

                    <button id="addCartBtn"
                        class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow disabled:opacity-50"
                        disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="w-6 h-6"
                            fill="currentColor">
                            <!-- currentColor sẽ là màu trắng từ text-white -->
                            <path
                                d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                        </svg>
                        Thêm vào giỏ
                    </button>


                    <!-- Nút yêu thích -->
                    <button id="favBtn" data-favourited="<?= $isFav ? '1' : '0'; ?>" class="flex items-center gap-2 px-6 py-2 bg-white border font-semibold rounded-lg shadow transition
            <?= $isFav
                    ? 'text-red-500 border-red-500'
                    : 'text-blue-600 border-blue-500'; ?>"
                        title="<?= $isFav ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích'; ?>">
                        <?php if ($isFav): ?>
                        <!-- Solid heart (màu đỏ) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-6 h-6"
                            fill="currentColor">
                            <path
                                d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z" />
                        </svg>
                        <?php else: ?>
                        <!-- Outline heart (màu xanh) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-6 h-6"
                            fill="currentColor">
                            <path
                                d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2z" />
                        </svg>
                        <?php endif; ?>
                        <span><?= $isFav ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích'; ?></span>
                    </button>


                </div>

                <!-- Mô tả sản phẩm -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h2 class="text-xl font-semibold mb-2 text-gray-800">Mô tả sản phẩm</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($product['description']); ?>
                    </p>
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
const sizeOpts = document.getElementById('sizeOptions');

// Giới hạn số lượng nhập
qtyInput.addEventListener('input', () => {
    const max = parseInt(qtyInput.max);
    let v = parseInt(qtyInput.value) || 1;
    if (v > max) v = max;
    if (v < 1) v = 1;
    qtyInput.value = v;
    addBtn.disabled = !selectedColor || (sizeOpts.childElementCount > 0 && !selectedSize) || v < 1;
});

// Chọn màu
function selectColor(cid) {
    selectedColor = cid;
    selectedSize = '';
    document.querySelectorAll('#colorOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    document.getElementById('color-' + cid).classList.add('ring-2', 'ring-blue-500');
    updateSizeOptions(cid);
}

// Cập nhật size
function updateSizeOptions(cid) {
    sizeOpts.innerHTML = '';
    const list = variants.filter(v => v.color_id === cid && v.size_name);
    if (!list.length) {
        addBtn.disabled = false;
        stockEl.textContent = totalStock;
        qtyInput.max = totalStock;
        return;
    }
    list.sort((a, b) => sizeOrder.indexOf(a.size_name) - sizeOrder.indexOf(b.size_name));
    list.forEach(v => {
        const btn = document.createElement('button');
        btn.textContent = v.size_name;
        btn.className = 'w-12 h-10 rounded-lg border text-sm font-semibold shadow-sm transition';
        if (parseInt(v.quantity) === 0) {
            btn.classList.add('crossed-out', 'bg-gray-100', 'text-gray-400');
        } else {
            btn.onclick = () => selectSize(v);
        }
        sizeOpts.appendChild(btn);
    });
}

// Chọn size
function selectSize(v) {
    selectedSize = v.size_id;
    document.querySelectorAll('#sizeOptions button').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
    event.currentTarget.classList.add('ring-2', 'ring-blue-500');
    const q = parseInt(v.quantity);
    stockEl.textContent = q;
    qtyInput.max = q;
    qtyInput.value = Math.min(parseInt(qtyInput.value), q);
    addBtn.disabled = q === 0;
}

// Xử lý yêu thích
favBtn.addEventListener('click', async () => {
    const isFavNow = favBtn.dataset.favourited === '1';
    const action = isFavNow ? 'remove' : 'add';
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

    // Toggle trạng thái
    favBtn.dataset.favourited = isFavNow ? '0' : '1';

    if (!isFavNow) {
        // Đã thêm vào yêu thích ⇒ chuyển sang đỏ / nút “Xóa khỏi yêu thích”
        favBtn.classList.remove('text-blue-600', 'border-blue-500');
        favBtn.classList.add('text-red-500', 'border-red-500');
        favBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
            class="w-6 h-6 text-red-500"
            fill="currentColor">
            <path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/>
        </svg>
        <span>Xóa khỏi yêu thích</span>
        `;
    } else {
        // Đã bỏ yêu thích ⇒ chuyển về xanh / nút “Thêm vào yêu thích”
        favBtn.classList.remove('text-red-500', 'border-red-500');
        favBtn.classList.add('text-blue-600', 'border-blue-500');
        favBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
            class="w-6 h-6 text-blue-600"
            fill="currentColor">
            <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2z"/>
        </svg>
        <span>Thêm vào yêu thích</span>
        `;
    }

    alert(data.message);
});



function showToast(message, productName, imageUrl) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
            <div class="toast-content">
                <img src="${imageUrl}" alt="${productName}" class="toast-image">
                <div class="toast-text">
                    <strong>Đã thêm vào giỏ hàng!</strong>
                    <p><?= htmlspecialchars($product['product_name']); ?></p>
                </div>
            </div>
        `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 4000);
}

function addToCart() {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', '<?= $product_id;?>');
    formData.append('color_id', selectedColor);
    formData.append('size_id', selectedSize);
    formData.append('quantity', qtyInput.value);

    fetch('<?= USER_URL ?>/controller/cartController.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                const cnt = document.getElementById('cart-count');
                if (cnt) cnt.textContent = d.data.items.length;

                showToast(
                    'Đã thêm vào giỏ hàng!',
                    '<?= htmlspecialchars($product['product_name']); ?>',
                    '../<?= htmlspecialchars($product['main_image']); ?>' // Thay bằng đường dẫn ảnh sản phẩm
                );
            } else {
                alert(d.message || 'Có lỗi xảy ra');
            }
        }).catch(e => {
            console.error(e);
            alert('Lỗi mạng, vui lòng thử lại!');
        });
}


// Gán sự kiện sau khi DOM load
document.addEventListener('DOMContentLoaded', () => {
    // ẩn màu hết hàng
    [...new Set(variants.map(v => v.color_id))]
    .forEach(cid => {
        if (!variants.some(v => v.color_id === cid && parseInt(v.quantity) > 0)) {
            document.getElementById('color-' + cid).classList.add('crossed-out');
        }
    });
    // tự chọn màu đầu tiên còn hàng
    const available = variants.find(v => parseInt(v.quantity) > 0);
    if (available) selectColor(available.color_id);

    // bật sự kiện nút thêm vào giỏ
    addBtn.addEventListener('click', addToCart);
});
    </script>