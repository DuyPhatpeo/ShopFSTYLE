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
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-semibold">Thêm biến thể cho sản phẩm</h2>
        <a href="index.php"
            class="flex items-center gap-2 bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
            ← Quay lại danh sách
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

        <!-- Chọn màu -->
        <div class="mb-6">
            <label class="block mb-2 font-medium text-lg">Chọn màu sắc</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($allColors as $color): ?>
                <?php
                        $colorCodes = explode(',', $color['color_code']);
                        $gradientParts = [];
                        $count = count($colorCodes);
                        $percent = floor(100 / $count);
                        $start = 0;
                        foreach ($colorCodes as $i => $code) {
                            $code = trim($code);
                            $end = ($i === $count - 1) ? 100 : $start + $percent;
                            $gradientParts[] = "$code $start% $end%";
                            $start = $end;
                        }
                        $gradient = implode(', ', $gradientParts);
                    ?>
                <label class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="colors[]" value="<?= $color['color_id'] ?>"
                        class="color-checkbox hidden">
                    <span class="inline-block w-6 h-6 rounded-full border"
                        style="background: linear-gradient(90deg, <?= htmlspecialchars($gradient) ?>);"></span>
                    <span class="color-label"><?= htmlspecialchars($color['color_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chọn size -->
        <div class="mb-6">
            <label class="block mb-2 font-medium text-lg">Chọn kích thước (tuỳ chọn)</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <?php foreach ($allSizes as $size): ?>
                <label class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="sizes[]" value="<?= $size['size_id'] ?>" class="form-checkbox hidden">
                    <span><?= htmlspecialchars($size['size_name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nút tạo biến thể -->
        <div class="mb-6 text-center">
            <button type="button" onclick="generateVariants()"
                class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-600">
                Tạo biến thể
            </button>
        </div>

        <!-- Vùng hiển thị biến thể -->
        <div id="variant-output" class="mb-8"></div>

        <!-- Nút điều hướng -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <a href="show.php?product_id=<?= htmlspecialchars($product_id) ?>"
                class="bg-yellow-400 text-black px-6 py-3 rounded hover:bg-yellow-500">
                ← Quay lại chi tiết
            </a>

            <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600">
                Lưu biến thể
            </button>
        </div>
    </form>
</div>

<script>
// Hiệu ứng chọn checkbox
document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function() {
        this.parentElement.classList.toggle('bg-blue-100', this.checked);
    });
});

function generateVariants() {
    const selectedColors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(i => i.value);
    const selectedSizes = Array.from(document.querySelectorAll('input[name="sizes[]"]:checked')).map(i => i.value);
    const output = document.getElementById('variant-output');
    output.innerHTML = '';

    if (selectedColors.length === 0) {
        alert("Bạn phải chọn ít nhất một màu.");
        return;
    }

    selectedColors.forEach(colorId => {
        const colorLabel = document.querySelector(`input[name="colors[]"][value="${colorId}"]`)
            .parentElement.querySelector('.color-label').textContent;

        if (selectedSizes.length === 0) {
            const key = `${colorId}_`;
            output.appendChild(createVariantElement(key, colorLabel, 'Mặc định'));
        } else {
            selectedSizes.forEach(sizeId => {
                const sizeLabel = document.querySelector(`input[name="sizes[]"][value="${sizeId}"]`)
                    .parentElement.querySelector('span:last-child').textContent;
                const key = `${colorId}_${sizeId}`;
                output.appendChild(createVariantElement(key, colorLabel, sizeLabel));
            });
        }
    });
}

function createVariantElement(key, colorLabel, sizeLabel) {
    const div = document.createElement('div');
    div.setAttribute("role", "variant");
    div.className = 'grid grid-cols-1 sm:grid-cols-4 items-center gap-4 mb-4 p-4 border rounded-lg bg-gray-50';

    div.innerHTML = `
        <div><strong>Màu:</strong> ${colorLabel}</div>
        <div><strong>Size:</strong> ${sizeLabel}</div>
        <div>
            <label class="block font-medium mb-1">Số lượng</label>
            <input type="number" name="variants[${key}][quantity]" required min="1" value="1"
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="text-right">
            <button type="button" onclick="removeVariant(this)"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Xóa
            </button>
        </div>
    `;
    return div;
}

function removeVariant(btn) {
    btn.closest('div[role="variant"]').remove();
}
</script>

<?php include('../../includes/footer.php'); ob_end_flush(); ?>