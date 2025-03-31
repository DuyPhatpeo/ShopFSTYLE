<?php 
$pageTitle = "Thêm biến thể sản phẩm";

include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Kiểm tra GET parameter 'pid'
if (!isset($_GET['pid'])) {
    header("Location: index.php?msg=ID sản phẩm không hợp lệ.&type=failure");
    exit;
}

$product_id = $_GET['pid'];
$product = getProductById($conn, $product_id);
if (!$product) {
    header("Location: index.php?msg=Sản phẩm không tồn tại.&type=failure");
    exit;
}

// Lấy danh sách màu và kích cỡ từ cơ sở dữ liệu
$allColors = getAllColors($conn);
$allSizes = getAllSizes($conn);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processAddVariantsStep2($conn, $product_id, $_POST, $_FILES, $errors);
    if (empty($errors)) {
        // Sau khi thêm biến thể xong, chuyển sang trang thêm ảnh cho từng biến thể theo màu
        header("Location: add_variant_images.php?pid=" . urlencode($product_id));
        exit;
    }
}
?>

<main class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Thêm Biến Thể Cho Sản Phẩm</h1>
        <a href="index.php"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded shadow transition duration-150">
            Quay lại danh sách sản phẩm
        </a>
    </div>

    <div class="mb-6 p-4 bg-gray-100 rounded">
        <p class="text-lg">Sản phẩm: <strong><?= htmlspecialchars($product['product_name']) ?></strong></p>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow-lg rounded p-6">
        <!-- Chọn màu sắc -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Chọn màu sắc</h2>
            <div class="flex flex-wrap gap-4">
                <?php foreach ($allColors as $color): ?>
                <label class="flex items-center gap-2 bg-white p-2 rounded shadow transition duration-150">
                    <input type="checkbox" class="colorCheckbox" value="<?= htmlspecialchars($color['color_id']) ?>">
                    <span><?= htmlspecialchars($color['color_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chọn kích cỡ -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Chọn kích cỡ</h2>
            <div class="flex flex-wrap gap-4">
                <?php foreach ($allSizes as $size): ?>
                <label class="flex items-center gap-2 bg-white p-2 rounded shadow transition duration-150">
                    <input type="checkbox" class="sizeCheckbox" value="<?= htmlspecialchars($size['size_id']) ?>">
                    <span><?= htmlspecialchars($size['size_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nút tạo bảng kết hợp biến thể -->
        <div class="mb-6">
            <button type="button" id="generateVariants"
                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow transition">
                Tạo các kết hợp biến thể
            </button>
        </div>

        <!-- Bảng hiển thị các biến thể được tạo tự động -->
        <div id="variantsContainer" class="mb-6 hidden">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Nhập số lượng cho từng biến thể</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 border text-left">Màu</th>
                        <th class="p-2 border text-left">Kích cỡ</th>
                        <th class="p-2 border text-left">Số lượng</th>
                    </tr>
                </thead>
                <tbody id="variantTable">
                    <!-- Các dòng được tạo tự động -->
                </tbody>
            </table>
        </div>

        <!-- Nút submit -->
        <div class="flex justify-end">
            <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded shadow transition duration-150">
                Lưu biến thể
            </button>
        </div>
    </form>
</main>

<script>
// Thêm sự kiện đổi màu nền cho checkbox khi chọn/bỏ chọn
document.querySelectorAll('.colorCheckbox, .sizeCheckbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            this.parentElement.classList.add('bg-blue-100');
        } else {
            this.parentElement.classList.remove('bg-blue-100');
        }
    });
});

// Hàm tạo bảng kết hợp giữa màu và kích cỡ đã chọn
document.getElementById('generateVariants').addEventListener('click', function() {
    var selectedColors = Array.from(document.querySelectorAll('.colorCheckbox:checked')).map(function(cb) {
        return {
            id: cb.value,
            name: cb.parentElement.textContent.trim()
        };
    });
    var selectedSizes = Array.from(document.querySelectorAll('.sizeCheckbox:checked')).map(function(cb) {
        return {
            id: cb.value,
            name: cb.parentElement.textContent.trim()
        };
    });

    // Nếu không có màu nào được chọn, bắt buộc thông báo lỗi
    if (selectedColors.length === 0) {
        alert("Vui lòng chọn ít nhất 1 màu sắc!");
        return;
    }
    // Nếu không có kích cỡ nào được chọn, tạo một kết hợp với giá trị "Không có kích cỡ"
    if (selectedSizes.length === 0) {
        selectedSizes.push({
            id: null,
            name: "Không có kích cỡ"
        });
    }

    var variants = [];
    selectedColors.forEach(function(color) {
        selectedSizes.forEach(function(size) {
            variants.push({
                color: color,
                size: size
            });
        });
    });

    var tableBody = document.getElementById('variantTable');
    tableBody.innerHTML = "";
    variants.forEach(function(variant) {
        var tr = document.createElement('tr');
        tr.classList.add("border-b");

        // Cột màu
        var tdColor = document.createElement('td');
        tdColor.classList.add("p-2", "border");
        var inputColor = document.createElement('input');
        inputColor.type = "hidden";
        inputColor.name = "variant_color[]";
        inputColor.value = variant.color.id;
        tdColor.appendChild(inputColor);
        tdColor.appendChild(document.createTextNode(variant.color.name));
        tr.appendChild(tdColor);

        // Cột kích cỡ
        var tdSize = document.createElement('td');
        tdSize.classList.add("p-2", "border");
        var inputSize = document.createElement('input');
        inputSize.type = "hidden";
        inputSize.name = "variant_size[]";
        inputSize.value = (variant.size.id !== null) ? variant.size.id : "";
        tdSize.appendChild(inputSize);
        tdSize.appendChild(document.createTextNode(variant.size.name));
        tr.appendChild(tdSize);

        // Cột số lượng
        var tdQuantity = document.createElement('td');
        tdQuantity.classList.add("p-2", "border");
        var inputQty = document.createElement('input');
        inputQty.type = "number";
        inputQty.name = "variant_quantity[]";
        inputQty.placeholder = "Nhập số lượng";
        inputQty.classList.add("w-full", "p-1", "border", "rounded");
        inputQty.min = "0";
        tdQuantity.appendChild(inputQty);
        tr.appendChild(tdQuantity);

        tableBody.appendChild(tr);
    });

    document.getElementById('variantsContainer').classList.remove('hidden');
});
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>