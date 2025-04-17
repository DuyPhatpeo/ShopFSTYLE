<?php
// File: slider_leaf.php

// Kết nối CSDL
require_once '../../includes/db.php';
require_once '../../model/categoryModel.php';

// Tạo đối tượng CategoryModel
$categoryModel = new CategoryModel($conn);

// Lấy danh mục cha
$parentCategories = $categoryModel->getParentCategories();

// Tập hợp danh mục leaf: gồm các danh mục cha không có con và các danh mục con
$leafCategories = [];

foreach ($parentCategories as $parent) {
    $children = $categoryModel->getChildCategories($parent['category_id']);
    
    // Nếu không có con (tức là danh mục cha này là leaf)
    if (count($children) === 0) {
        $leafCategories[] = $parent;
    } else {
        // Có con thì thêm các con vào danh sách leaf
        foreach ($children as $child) {
            $leafCategories[] = $child;
        }
    }
}
?>
<style>
.scroll-hidden::-webkit-scrollbar {
    display: none;
}

.scroll-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.nav-btn {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
</style>

<div class="relative px-2 mt-8 max-w-screen-2xl mx-auto">
    <!-- Nút điều hướng slider -->
    <button onclick="scrollSlider(-1)"
        class="nav-btn absolute -left-10 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md rounded-full hover:bg-gray-200 transition">
        &#8592;
    </button>
    <button onclick="scrollSlider(1)"
        class="nav-btn absolute -right-10 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-md rounded-full hover:bg-gray-200 transition">
        &#8594;
    </button>

    <!-- Slider danh mục leaf -->
    <div id="leafSlider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory pb-4 scroll-hidden scroll-smooth">
        <?php if (!empty($leafCategories)): ?>
        <?php foreach ($leafCategories as $cat): ?>
        <div class="snap-start flex-shrink-0 w-[350px] group">
            <a href="views/category_products.php?id=<?php echo urlencode($cat['category_id']); ?>">
                <div class="w-full h-[500px] mt-2">
                    <img class="w-full h-full object-cover rounded-xl transition-transform duration-300 group-hover:scale-105"
                        src="<?php echo htmlspecialchars($cat['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($cat['category_name']); ?>" />
                </div>
            </a>
            <a href="views/category_products.php?id=<?php echo urlencode($cat['category_id']); ?>">
                <h2
                    class="text-lg font-semibold text-gray-800 text-center mt-2 transition-colors duration-300 group-hover:text-blue-600">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </h2>
            </a>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-center text-gray-600">Không có danh mục leaf nào.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function scrollSlider(direction) {
    const slider = document.getElementById('leafSlider');
    const scrollAmount = 400;
    slider.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}
</script>