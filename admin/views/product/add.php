<?php 
// File: admin/views/product/add.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = processAddProductStep1($conn, $_POST, $_FILES, $errors);
    if ($product_id && empty($errors)) {
        header("Location: ../product_variant/add.php?product_id=" . $product_id);
        exit;
    }
}

$brands     = getAllBrands($conn);
$categories = getAllCategories($conn);
?>
<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Sản Phẩm (Bước 1)</h1>
        </div>
        <a href="index.php" class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            <!-- Icon quay lại theo yêu cầu -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 128 9.4 233.4z" />
            </svg>
            <span>Quay lại</span>
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="text-red-600 mb-2"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
        <div class="flex flex-wrap -mx-2">
            <!-- Cột trái -->
            <div class="w-full md:w-1/2 px-2 space-y-4">
                <div>
                    <label for="product_name" class="block font-medium mb-1">Tên sản phẩm <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="product_name" name="product_name"
                        class="border p-2 w-full <?= !empty($errors['product_name']) ? 'border-red-500' : 'border-gray-300' ?>"
                        value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>">
                    <?php if (!empty($errors['product_name'])): ?>
                    <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['product_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="brand_id" class="block font-medium mb-1">Thương hiệu <span
                            class="text-red-500">*</span></label>
                    <select id="brand_id" name="brand_id" class="border p-2 w-full">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= htmlspecialchars($brand['brand_id']) ?>"
                            <?= (isset($_POST['brand_id']) && $_POST['brand_id'] == $brand['brand_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['brand_id'])): ?>
                    <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['brand_id']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="category_id" class="block font-medium mb-1">Danh mục <span
                            class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" class="border p-2 w-full">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['category_id']) ?>"
                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?>
                    <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['category_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Cột phải -->
            <div class="w-full md:w-1/2 px-2 space-y-4">
                <div>
                    <label for="original_price" class="block font-medium mb-1">Giá gốc <span
                            class="text-red-500">*</span></label>
                    <input type="number" id="original_price" name="original_price" step="0.01" class="border p-2 w-full"
                        value="<?= htmlspecialchars($_POST['original_price'] ?? '') ?>">
                    <?php if (!empty($errors['original_price'])): ?>
                    <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['original_price']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="discount_price" class="block font-medium mb-1">Giá khuyến mãi <span
                            class="text-red-500">*</span></label>
                    <input type="number" id="discount_price" name="discount_price" step="0.01" class="border p-2 w-full"
                        value="<?= htmlspecialchars($_POST['discount_price'] ?? '') ?>">
                    <?php if (!empty($errors['discount_price'])): ?>
                    <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['discount_price']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- CKEditor cho mô tả -->
        <div>
            <label for="description" class="block font-medium mb-1">Mô tả <span class="text-red-500">*</span></label>
            <textarea id="editor" name="descriptionProduct"
                rows="6"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
            <p class="text-red-500 text-sm"><?= htmlspecialchars($errors['description']) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <span>Tiếp tục</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 ml-1" fill="currentColor">
                    <path
                        d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.3 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                </svg>
            </button>
        </div>
    </form>
</main>

<?php 
include("../../includes/footer.php");
ob_end_flush();
?>

<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: [
            'undo', 'redo', '|',
            'heading', '|',
            'fontSize', 'bold', 'italic', 'link', '|',
            'alignment', 'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'mediaEmbed', 'imageUpload'
        ],
        alignment: {
            options: ['left', 'center', 'right', 'justify']
        },
        fontSize: {
            options: [10, 12, 'default', 14, 16, 18, 20, 24, 28, 32],
            supportAllValues: true
        },
        image: {
            styles: ['full', 'alignLeft', 'alignCenter', 'alignRight'],
            toolbar: [
                'imageTextAlternative', '|',
                'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight'
            ]
        },
        simpleUpload: {
            uploadUrl: '/ShopFSTYLE/admin/your-upload-endpoint.php',
            headers: {
                'X-CSRF-TOKEN': 'CSRF-Token'
            }
        }
    })
    .catch(error => {
        console.error(error);
    });
</script>