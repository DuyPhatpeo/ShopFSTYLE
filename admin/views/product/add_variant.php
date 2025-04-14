<?php
ob_start();
$pageTitle = "Trang thêm biến thể cho sản phẩm";
include("../../includes/session_check.php");
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/variantController.php');

$product_id = $_GET['product_id'] ?? '';
$allColors = getAllColors($conn);
$allSizes = getAllSizes($conn);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processAddVariant($conn);
    if (is_array($result)) {
        $errors = $result;
    } else {
        header("Location: ../product/index.php");
        exit;
    }
}
?>

<div class="container mx-auto p-6">
    <!-- Tiêu đề và nút "Quay lại danh sách" ở góc trên bên phải -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-semibold">Thêm biến thể cho sản phẩm</h2>
        <a href="index.php"
            class="flex items-center gap-2 bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-4 h-4 fill-current">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            Quay lại danh sách
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 text-red-600 p-4 rounded mb-6">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">

        <!-- Chọn màu sắc -->
        <div class="mb-6">
            <label class="block mb-2 font-medium text-lg">Chọn màu sắc</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($allColors as $color): ?>
                <?php
                        // Nếu color_code có nhiều mã màu, tách chúng
                        $colorCodes = explode(',', $color['color_code']);

                        // Chuẩn bị chuỗi gradient: chia đều phần trăm cho từng màu.
                        $gradientParts = [];
                        $count = count($colorCodes);
                        $percent = floor(100 / $count);
                        $start = 0;
                        foreach ($colorCodes as $i => $code) {
                            $code = trim($code);
                            $end = $start + $percent;
                            if ($i === $count - 1) {
                                $end = 100; // đảm bảo 100%
                            }
                            $gradientParts[] = "$code $start% $end%";
                            $start = $end;
                        }
                        $gradient = implode(', ', $gradientParts);
                    ?>
                <label
                    class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                    <input type="checkbox" name="colors[]" value="<?= $color['color_id'] ?>"
                        class="color-checkbox w-4 h-4 hidden"
                        data-color="<?= htmlspecialchars($color['color_code']) ?>">

                    <!-- Chấm tròn duy nhất với nền linear-gradient -->
                    <span class="inline-block w-6 h-6 rounded-full border"
                        style="background: linear-gradient(90deg, <?= htmlspecialchars($gradient) ?>);"></span>

                    <span class="color-label"><?= htmlspecialchars($color['color_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chọn kích thước -->
        <div class="mb-6">
            <label class="block mb-2 font-medium text-lg">Chọn kích thước</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($allSizes as $size): ?>
                <label
                    class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                    <input type="checkbox" name="sizes[]" value="<?= $size['size_id'] ?>" class="form-checkbox hidden">
                    <span><?= htmlspecialchars($size['size_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nút tạo biến thể -->
        <div class="mb-6 text-center">
            <button type="button" onclick="generateVariants()"
                class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Tạo biến thể
            </button>
        </div>

        <!-- Kết quả biến thể -->
        <div id="variant-output" class="mb-8">
            <!-- Các biến thể sẽ được render ở đây -->
        </div>

        <!-- Các nút điều hướng và lưu -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <!-- Quay lại chi tiết -->
            <a href="show.php?product_id=<?= htmlspecialchars($product_id) ?>"
                class="flex items-center gap-2 bg-yellow-400 text-black px-6 py-3 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-600 text-center w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 fill-current">
                    <path
                        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                </svg>
                Quay lại chi tiết
            </a>

            <!-- Lưu biến thể -->
            <button type="submit"
                class="flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-lg shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 fill-current">
                    <path
                        d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-242.7c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32L64 32zm0 96c0-17.7 14.3-32 32-32l192 0c17.7 0 32 14.3 32 32l0 64c0 17.7-14.3 32-32 32L96 224c-17.7 0-32-14.3-32-32l0-64zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                </svg>
                Lưu biến thể
            </button>
        </div>
    </form>
</div>

<script>
// Sự kiện cho checkbox: thêm/removing class highlight
document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            this.parentElement.classList.add('bg-blue-100');
        } else {
            this.parentElement.classList.remove('bg-blue-100');
        }
    });
});

// Hàm tạo danh sách biến thể kèm nút xóa cho từng dòng
function generateVariants() {
    const selectedColors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(i => i.value);
    const selectedSizes = Array.from(document.querySelectorAll('input[name="sizes[]"]:checked')).map(i => i.value);
    const output = document.getElementById('variant-output');
    output.innerHTML = '';

    if (selectedColors.length === 0 || selectedSizes.length === 0) {
        alert("Bạn phải chọn ít nhất một màu và một kích thước.");
        return;
    }

    selectedColors.forEach(colorId => {
        selectedSizes.forEach(sizeId => {
            // Lấy nhãn màu và size
            const colorLabel = document.querySelector(`input[name="colors[]"][value="${colorId}"]`)
                .parentElement.querySelector('.color-label').textContent;
            const sizeLabelElement = document.querySelector(`input[name="sizes[]"][value="${sizeId}"]`)
                .parentElement.querySelector('span:last-child');
            const sizeLabel = sizeLabelElement ? sizeLabelElement.textContent : '';

            // Tạo div chứa thông tin biến thể và nút xóa
            const variantDiv = document.createElement('div');
            variantDiv.setAttribute("role", "variant");
            variantDiv.className =
                'grid grid-cols-1 sm:grid-cols-4 items-center gap-4 mb-4 p-4 border rounded-lg bg-gray-50';

            variantDiv.innerHTML = `
                <div><strong>Màu:</strong> ${colorLabel}</div>
                <div><strong>Size:</strong> ${sizeLabel}</div>
                <div>
                    <label class="block font-medium mb-1">Số lượng</label>
                    <input type="number" name="variants[${colorId}_${sizeId}][quantity]" required min="1"
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" value="1">
                </div>
                <div class="text-right">
                    <button type="button" onclick="removeVariant(this)"
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Xóa
                    </button>
                </div>
            `;
            output.appendChild(variantDiv);
        });
    });
}

// Hàm xóa một biến thể dựa vào thuộc tính role
function removeVariant(button) {
    const variant = button.closest('div[role="variant"]');
    if (variant) {
        variant.remove();
    }
}
</script>

<?php 
include('../../includes/footer.php');
ob_end_flush();
?>