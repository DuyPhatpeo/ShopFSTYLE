<?php
function renderPagination($currentPage, $totalPages, $limit) {
    $delta = 2;
    $range = [];
    for ($i = max(1, $currentPage - $delta); $i <= min($totalPages, $currentPage + $delta); $i++) {
        $range[] = $i;
    }
    if (!in_array(1, $range)) {
        array_unshift($range, 1);
        if ($range[1] != 2) {
            array_splice($range, 1, 0, '...');
        }
    }
    if (!in_array($totalPages, $range)) {
        if (end($range) != $totalPages - 1) {
            $range[] = '...';
        }
        $range[] = $totalPages;
    }
    ?>
<nav class="flex items-center space-x-2">
    <!-- Nút "Trang đầu tiên" -->
    <?php if ($currentPage > 1): ?>
    <a href="?page=1&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-300 flex items-center">
        <!-- Icon: Double Chevron Left -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
        </svg>
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded cursor-not-allowed flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
        </svg>
    </span>
    <?php endif; ?>

    <!-- Nút "Trang trước" -->
    <?php if ($currentPage == 1): ?>
    <span
        class="px-3 py-1 bg-gray-200 text-gray-700 rounded cursor-not-allowed transition-colors duration-300 flex items-center">
        <!-- Icon: Chevron Left -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </span>
    <?php else: ?>
    <a href="?page=<?= $currentPage - 1 ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-300 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <?php endif; ?>

    <!-- Dải trang (range) -->
    <?php foreach ($range as $page): ?>
    <?php if ($page === '...'): ?>
    <span class="px-3 py-1">...</span>
    <?php else: ?>
    <?php if ($page == $currentPage): ?>
    <span class="px-3 py-1 bg-indigo-500 text-white rounded transition-colors duration-300"><?= $page ?></span>
    <?php else: ?>
    <a href="?page=<?= $page ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-300"><?= $page ?></a>
    <?php endif; ?>
    <?php endif; ?>
    <?php endforeach; ?>

    <!-- Nút "Trang kế" -->
    <?php if ($currentPage == $totalPages): ?>
    <span
        class="px-3 py-1 bg-gray-200 text-gray-700 rounded cursor-not-allowed transition-colors duration-300 flex items-center">
        <!-- Icon: Chevron Right -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5l7 7-7 7" />
        </svg>
    </span>
    <?php else: ?>
    <a href="?page=<?= $currentPage + 1 ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-300 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5l7 7-7 7" />
        </svg>
    </a>
    <?php endif; ?>

    <!-- Nút "Trang cuối" -->
    <?php if ($currentPage < $totalPages): ?>
    <a href="?page=<?= $totalPages ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-300 flex items-center">
        <!-- Icon: Double Chevron Right -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 5l7 7-7 7M6 5l7 7-7 7" />
        </svg>
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded cursor-not-allowed flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 5l7 7-7 7M6 5l7 7-7 7" />
        </svg>
    </span>
    <?php endif; ?>
</nav>
<?php
}
?>