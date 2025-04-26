<?php
ob_start();
$pageTitle = "Trang thêm sản phẩm";
include("../../includes/session_check.php");
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');
require_once('../../controller/brandController.php');
require_once('../../controller/categoryController.php');

$allBrands     = getAllBrands($conn);
$allCategories = getAllCategories($conn);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = processAddProduct($conn);
}
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Thêm Sản Phẩm</h1>
        <!-- Quay lại -->
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" viewBox="0 0 448 512" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H109.2L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L9.4 233.4z" />
            </svg>
            <span class="hidden md:inline">Quay lại</span>
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white rounded shadow p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LEFT -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="product_name" required
                        value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>"
                        class="w-full p-3 border rounded <?= !empty($errors['product_name']) ? 'border-red-500' : 'border-gray-300'; ?>">
                    <?php if (!empty($errors['product_name'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['product_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span
                            class="text-red-500">*</span></label>
                    <select name="category_id" required
                        class="w-full p-3 border rounded <?= !empty($errors['category_id']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"
                            <?= (($_POST['category_id'] ?? '') == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['category_id']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu <span
                            class="text-red-500">*</span></label>
                    <select name="brand_id" required
                        class="w-full p-3 border rounded <?= !empty($errors['brand_id']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($allBrands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>"
                            <?= (($_POST['brand_id'] ?? '') == $brand['brand_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['brand_id'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['brand_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá gốc (VND) <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="original_price" required
                        value="<?= htmlspecialchars($_POST['original_price'] ?? '') ?>"
                        class="w-full p-3 border rounded <?= !empty($errors['original_price']) ? 'border-red-500' : 'border-gray-300'; ?>">
                    <?php if (!empty($errors['original_price'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['original_price']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi</label>
                    <input type="number" name="discount_price"
                        value="<?= htmlspecialchars($_POST['discount_price'] ?? '') ?>"
                        class="w-full p-3 border rounded <?= !empty($errors['discount_price']) ? 'border-red-500' : 'border-gray-300'; ?>">
                    <?php if (!empty($errors['discount_price'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['discount_price']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span
                            class="text-red-500">*</span></label>
                    <select name="status" required class="w-full p-3 border rounded border-gray-300">
                        <option value="1" <?= (($_POST['status'] ?? 1) == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="2" <?= (($_POST['status'] ?? 1) == 2) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả sản phẩm</label>
            <textarea name="description" rows="4" required
                class="w-full p-3 border rounded <?= !empty($errors['description']) ? 'border-red-500' : 'border-gray-300'; ?>"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Ảnh sản phẩm -->
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-3">
                Ảnh sản phẩm <span class="text-xs text-gray-500">(Kéo thả để sắp xếp)</span>
            </label>
            <div class="overflow-x-auto">
                <div id="image-upload-wrapper" class="space-y-4 min-w-[300px] w-full"></div>
            </div>
            <button type="button" onclick="addMoreImageInput()"
                class="mt-3 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-md shadow-sm hover:bg-blue-600">
                + Thêm ảnh
            </button>
            <?php if (!empty($errors['content-imgs'])): ?>
            <p class="mt-2 text-sm text-red-500"><?= htmlspecialchars($errors['content-imgs']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded">Hoàn thành sản
                phẩm</button>
        </div>
    </form>
</main>

<script>
const wrapper = document.getElementById('image-upload-wrapper');
let dragSrcEl = null;

function addMoreImageInput() {
    const group = document.createElement('div');
    group.className =
        'image-group flex flex-wrap sm:flex-nowrap items-center gap-4 p-4 border rounded-lg shadow-sm bg-white';

    group.innerHTML = `
      <input type="file" name="content-imgs[]" accept="image/*" required
             class="block w-full sm:w-48 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
             onchange="previewSingleImage(event, this)">
      <input type="number" name="positions[]" min="1" placeholder="Vị trí" required
             class="block p-2 border border-gray-300 rounded-md w-20 text-center">
      <label class="inline-flex items-center space-x-2 text-sm text-gray-700">
        <input type="radio" name="is_main" value="" required class="text-blue-600">
        <span>Ảnh chính</span>
      </label>
      <div class="preview-container w-48 h-48 rounded overflow-hidden border border-gray-200 bg-gray-50"></div>
    `;

    wrapper.appendChild(group);
    addDnDHandlers([group]);
    updatePositions();
}

function previewSingleImage(e, input) {
    const reader = new FileReader();
    reader.onload = ev => {
        input.closest('.image-group').querySelector('.preview-container').innerHTML =
            `<img src="${ev.target.result}" class="object-cover w-full h-full" alt="Preview">`;
    };
    reader.readAsDataURL(e.target.files[0]);
}

function updatePositions() {
    wrapper.querySelectorAll('.image-group').forEach((group, idx) => {
        group.querySelector('input[name="positions[]"]').value = idx + 1;
        group.querySelector('input[type="radio"][name="is_main"]').value = idx + 1;
    });
}

function addDnDHandlers(items) {
    items.forEach(item => {
        item.draggable = true;
        item.addEventListener('dragstart', e => {
            dragSrcEl = item;
            e.dataTransfer.effectAllowed = 'move';
            item.classList.add('opacity-50');
        });
        item.addEventListener('dragenter', () => item.classList.add('border-blue-400', 'border-dashed'));
        item.addEventListener('dragleave', () => item.classList.remove('border-blue-400', 'border-dashed'));
        item.addEventListener('dragover', e => e.preventDefault());
        item.addEventListener('drop', e => {
            e.preventDefault();
            if (dragSrcEl && dragSrcEl !== item) {
                const nodes = Array.from(wrapper.children);
                const from = nodes.indexOf(dragSrcEl),
                    to = nodes.indexOf(item);
                if (from < to) wrapper.insertBefore(dragSrcEl, item.nextSibling);
                else wrapper.insertBefore(dragSrcEl, item);
                updatePositions();
            }
        });
        item.addEventListener('dragend', () => {
            item.classList.remove('opacity-50');
            wrapper.querySelectorAll('.image-group').forEach(i => i.classList.remove('border-blue-400',
                'border-dashed'));
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    addMoreImageInput();
});
</script>

<?php include('../../includes/footer.php'); ob_end_flush(); ?>