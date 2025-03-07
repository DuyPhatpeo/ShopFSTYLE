<?php 
    include_once __DIR__ . "/../../includes/config.php";
    include ('../../includes/header.php'); 
    include ('../user/login-register.php');
    include ('../../includes/search.php');
?>
<style>
.out-of-stock {
    opacity: 0.5;
    pointer-events: none;
    position: relative;
}

.out-of-stock::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: red;
    transform: rotate(-45deg);
}

.zoomed {
    /* Khi zoom, scale 1.5, dịch chuyển theo giá trị (mặc định là 0,0) */
    transform: scale(1.5) translate(0px, 0px);
    cursor: grab;
}

/* CSS cho nút size hết hàng */
.disabled-size {
    opacity: 0.5;
    text-decoration: line-through;
    pointer-events: none;
}

/* CSS cho hướng dẫn trong modal */
#modalHint {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 1.125rem;
    background: rgba(0, 0, 0, 0.5);
    padding: 4px 8px;
    border-radius: 4px;
}
</style>

<!-- Chi tiết sản phẩm -->
<main class="max-w-7xl mx-auto p-6 bg-white shadow-md mt-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Hình ảnh sản phẩm -->
        <div class="flex">
            <div
                class="flex flex-col space-y-2 mr-4 overflow-y-auto max-h-96 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product1.png"
                    alt="Sản phẩm 1" class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product2.png"
                    alt="Sản phẩm 2"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product3.png"
                    alt="Sản phẩm 3"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product4.png"
                    alt="Sản phẩm 4"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product5.png"
                    alt="Sản phẩm 5"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
            </div>
            <div class="relative">
                <img id="mainImage" src="<?= USER_URL ?>/assets/images/public/product/product1.png" alt="Sản phẩm"
                    class="w-full h-auto object-cover cursor-pointer rounded-lg" onclick="openModal(this.src)">
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div>
            <h2 class="text-2xl font-bold">Quần dài kaki ECC Pants</h2>
            <p class="text-gray-600 mt-2">⭐⭐⭐⭐⭐ (4.9/5)</p>
            <p class="text-xl text-red-500 font-bold mt-4">599.000đ</p>
            <p class="mt-4">Màu sắc:</p>
            <div class="flex space-x-2 mt-2">
                <span class="color-swatch w-16 h-8 bg-black border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span class="color-swatch w-16 h-8 bg-gray-500 border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span
                    class="color-swatch w-16 h-8 bg-red-500 border border-gray-400 rounded-full cursor-pointer out-of-stock"></span>
                <span class="color-swatch w-16 h-8 bg-blue-500 border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span
                    class="color-swatch w-16 h-8 bg-green-500 border border-gray-400 rounded-full cursor-pointer out-of-stock"></span>





            </div>
            <p class="mt-4">Chọn size:</p>
            <!-- Nút chọn size -->
            <div class="flex space-x-4 mt-2">
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200" data-size="S">S</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200" data-size="M">M</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200" data-size="L">L</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200 disabled-size" data-size="XL"
                    disabled>XL</button>
            </div>

            <!-- Số lượng và nút thêm vào giỏ hàng chung một hàng -->
            <div class="flex items-center mt-4 space-x-2">
                <!-- Chọn số lượng sản phẩm kiểu pill với chiều rộng cố định -->
                <div class="flex items-center bg-gray-200 rounded-full p-1 space-x-2 w-32 justify-center">
                    <button id="decreaseQty"
                        class="px-3 py-1 bg-white rounded-full border border-gray-300 hover:bg-gray-300">-</button>
                    <span id="productQty" class="text-xl font-semibold">1</span>
                    <button id="increaseQty"
                        class="px-3 py-1 bg-white rounded-full border border-gray-300 hover:bg-gray-300">+</button>
                </div>
                <!-- Nút thêm vào giỏ hàng cố định -->
                <button
                    class="flex items-center justify-center bg-blue-600 text-white py-3 px-6 hover:bg-blue-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 inline-block mr-2 fill-current" fill="currentColor">
                        <path
                            d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                    </svg>
                    Thêm vào giỏ hàng
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Mô tả sản phẩm (bên ngoài main) -->
<section class="max-w-7xl mx-auto p-6 bg-gray-100 mt-6">
    <h3 class="text-xl font-bold">Mô tả sản phẩm</h3>
    <p class="mt-2 text-gray-700">Quần dài kaki ECC Pants mang lại sự thoải mái và phong cách hiện đại, phù hợp với
        nhiều dịp khác nhau.</p>
</section>

<!-- ĐÁNH GIÁ SẢN PHẨM (thay cho phần bình luận cũ) -->
<section class="max-w-7xl mx-auto p-6 bg-white mt-6 shadow-md rounded-md">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-bold">ĐÁNH GIÁ SẢN PHẨM</h3>
            <div class="flex items-center mt-2">
                <span class="text-3xl font-bold text-yellow-500">4.9</span>
                <div class="ml-2 flex">
                    <!-- 5 sao vàng -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                </div>
                <span class="ml-2 text-gray-500">Dựa trên 7 đánh giá từ khách hàng</span>
            </div>
        </div>
        <!-- Sắp xếp / Filter -->
        <div class="mt-4 sm:mt-0 flex space-x-4 items-center">
            <input type="text" placeholder="Tìm kiếm đánh giá"
                class="border p-2 rounded-md text-sm focus:outline-none" />
            <select class="border p-2 rounded-md text-sm focus:outline-none">
                <option value="">Tất cả sao</option>
                <option value="5">5 sao</option>
                <option value="4">4 sao</option>
                <option value="3">3 sao</option>
                <option value="2">2 sao</option>
                <option value="1">1 sao</option>
            </select>
            <select class="border p-2 rounded-md text-sm focus:outline-none">
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
            </select>
        </div>
    </div>

    <!-- Danh sách đánh giá -->
    <div class="mt-6 space-y-4">
        <div class="p-4 border rounded-lg">
            <div class="flex items-center justify-between">
                <div class="font-semibold">Phong Vẹn</div>
                <div class="text-sm text-gray-500">26/02/2025</div>
            </div>
            <div class="mt-1 flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
            </div>
            <p class="mt-2 text-gray-700">Quần mặc ổn, form đẹp, chuẩn size. Hài lòng!</p>
        </div>
        <!-- Có thể thêm nhiều đánh giá khác tùy ý -->
    </div>
</section>

<!-- Modal hiển thị ảnh lớn -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50">
    <button id="prevImage" class="absolute left-4 text-white text-3xl">&#10094;</button>
    <img id="modalImage" class="max-w-full max-h-full">
    <button id="nextImage" class="absolute right-4 text-white text-3xl">&#10095;</button>
    <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl">&times;</button>
    <div id="modalHint">Chạm vào ảnh để phóng to &amp; giữ chuột trái để di chuyển</div>
</div>

<script>
// Các biến toàn cục dùng cho modal ảnh
let currentImageIndex = 0;
const images = Array.from(document.querySelectorAll('.flex-col img'));

// Hàm cập nhật transform cho modal (scale và offset)
function updateModalTransform(scale, offsetX, offsetY) {
    document.getElementById('modalImage').style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
}

// Thay đổi ảnh chính và cập nhật trạng thái selected cho ảnh nhỏ
function changeImage(element) {
    document.getElementById('mainImage').src = element.src;
    images.forEach(img => img.classList.add('opacity-50'));
    element.classList.remove('opacity-50');
    currentImageIndex = images.indexOf(element);
}

document.querySelector('.flex-col img').classList.remove('opacity-50');

// Mở modal với ảnh được chọn và reset trạng thái zoom
function openModal(src) {
    const modalImg = document.getElementById('modalImage');
    modalImg.src = src;
    currentScale = 1;
    currentOffsetX = 0;
    currentOffsetY = 0;
    updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
    modalImg.style.cursor = 'zoom-in';
    document.getElementById('modalHint').textContent = 'Chạm vào ảnh để phóng to & giữ chuột trái để di chuyển';
    document.getElementById('imageModal').classList.remove('hidden');
}

// Đóng modal khi click ngoài ảnh hoặc nhấn nút đóng
function closeModal(event) {
    if (!event || event.target === document.getElementById('imageModal')) {
        document.getElementById('imageModal').classList.add('hidden');
    }
}

// Các biến điều khiển zoom và kéo
let currentScale = 1;
let currentOffsetX = 0;
let currentOffsetY = 0;
let isDragging = false;
let pointerDown = false;
let startX = 0,
    startY = 0;
const dragThreshold = 5; // pixel

const modalImage = document.getElementById('modalImage');

// Khi nhấn xuống, lưu vị trí ban đầu
modalImage.addEventListener('pointerdown', function(e) {
    pointerDown = true;
    isDragging = false;
    startX = e.clientX;
    startY = e.clientY;
    e.preventDefault();
});

// Khi di chuyển, nếu đã zoom (scale > 1) mới cho kéo
modalImage.addEventListener('pointermove', function(e) {
    if (!pointerDown) return;
    if (currentScale === 1) return; // Nếu chưa zoom thì không cho kéo
    let dx = e.clientX - startX;
    let dy = e.clientY - startY;
    if (!isDragging && (Math.abs(dx) > dragThreshold || Math.abs(dy) > dragThreshold)) {
        isDragging = true;
    }
    if (isDragging) {
        let newOffsetX = currentOffsetX + dx;
        let newOffsetY = currentOffsetY + dy;
        updateModalTransform(currentScale, newOffsetX, newOffsetY);
    }
    e.preventDefault();
});

// Khi nhả chuột: nếu chưa zoom thì zoom in, nếu đã zoom mà kéo thì cập nhật offset, còn không thì toggle zoom (zoom out)
modalImage.addEventListener('pointerup', function(e) {
    if (!pointerDown) return;
    if (currentScale === 1) {
        toggleZoom();
    } else {
        if (isDragging) {
            let dx = e.clientX - startX;
            let dy = e.clientY - startY;
            currentOffsetX += dx;
            currentOffsetY += dy;
            updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
        } else {
            toggleZoom();
        }
    }
    pointerDown = false;
    isDragging = false;
});

// Nếu pointer bị hủy
modalImage.addEventListener('pointercancel', function(e) {
    pointerDown = false;
});

// Hàm toggle zoom: nếu chưa zoom (scale=1) thì phóng to, ngược lại thu nhỏ và reset vị trí
function toggleZoom() {
    if (currentScale === 1) {
        currentScale = 1.5;
        modalImage.style.cursor = 'grab';
        document.getElementById('modalHint').textContent = 'Giữ chuột trái để di chuyển, chạm vào ảnh để thu nhỏ';
    } else {
        currentScale = 1;
        currentOffsetX = 0;
        currentOffsetY = 0;
        modalImage.style.cursor = 'zoom-in';
        document.getElementById('modalHint').textContent = 'Chạm vào ảnh để phóng to & giữ chuột trái để di chuyển';
    }
    updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
}

// Xử lý phím: Esc để đóng, ArrowLeft và ArrowRight để chuyển ảnh
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    } else if (event.key === "ArrowLeft") {
        showPrevImage();
    } else if (event.key === "ArrowRight") {
        showNextImage();
    }
});

document.getElementById('prevImage').addEventListener('click', showPrevImage);
document.getElementById('nextImage').addEventListener('click', showNextImage);

function showPrevImage() {
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    openModal(images[currentImageIndex].src);
}

function showNextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    openModal(images[currentImageIndex].src);
}

// Xử lý tăng giảm số lượng sản phẩm
const decreaseQty = document.getElementById('decreaseQty');
const increaseQty = document.getElementById('increaseQty');
const productQty = document.getElementById('productQty');

decreaseQty.addEventListener('click', () => {
    let qty = parseInt(productQty.textContent);
    if (qty > 1) {
        productQty.textContent = qty - 1;
    }
});

increaseQty.addEventListener('click', () => {
    let qty = parseInt(productQty.textContent);
    productQty.textContent = qty + 1;
});

// Xử lý chọn màu
function selectColor(element) {
    if (element.classList.contains("out-of-stock")) return; // Không cho chọn màu hết hàng

    const swatches = document.querySelectorAll('.color-swatch');
    swatches.forEach(el => {
        el.classList.remove('border-4', 'border-blue-500', 'selected');
        el.classList.add('border', 'border-gray-400');
    });

    element.classList.remove('border-gray-400');
    element.classList.add('border-4', 'border-blue-500', 'selected');
}

// Tự động chọn màu và ảnh đầu tiên khi trang được tải
window.onload = function() {
    const firstSwatch = document.querySelector('.color-swatch:not(.out-of-stock)'); // Chỉ chọn màu còn hàng
    if (firstSwatch) {
        selectColor(firstSwatch);
    }

    const firstImage = document.querySelector('.flex-col img');
    if (firstImage) {
        firstImage.classList.remove('opacity-50');
    }
};


// Xử lý chọn size
document.querySelectorAll('.size-button').forEach(function(button) {
    button.addEventListener('click', function() {
        if (this.hasAttribute('disabled')) return;
        document.querySelectorAll('.size-button').forEach(btn => {
            btn.classList.remove('bg-black', 'text-white');
            btn.classList.add('hover:bg-gray-200');
        });
        this.classList.add('bg-black', 'text-white');
        this.classList.remove('hover:bg-gray-200');
    });
});
</script>

<?php include ('../../includes/footer.php'); ?>