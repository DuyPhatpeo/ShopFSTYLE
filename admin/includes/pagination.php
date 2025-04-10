<?php
function renderPagination($currentPage, $totalPages, $limit) {
    if ($totalPages <= 1) return; // Không hiển thị nếu chỉ có 1 trang

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
    <!-- Trang đầu -->
    <?php if ($currentPage > 1): ?>
    <a href="?page=1&limit=<?= $limit ?>" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
        &laquo;
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded">&laquo;</span>
    <?php endif; ?>

    <!-- Trang trước -->
    <?php if ($currentPage > 1): ?>
    <a href="?page=<?= $currentPage - 1 ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
        &lsaquo;
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded">&lsaquo;</span>
    <?php endif; ?>

    <!-- Các số trang -->
    <?php foreach ($range as $page): ?>
    <?php if ($page === '...'): ?>
    <span class="px-3 py-1">...</span>
    <?php elseif ($page == $currentPage): ?>
    <span class="px-3 py-1 bg-indigo-500 text-white rounded"><?= $page ?></span>
    <?php else: ?>
    <a href="?page=<?= $page ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded"><?= $page ?></a>
    <?php endif; ?>
    <?php endforeach; ?>

    <!-- Trang sau -->
    <?php if ($currentPage < $totalPages): ?>
    <a href="?page=<?= $currentPage + 1 ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
        &rsaquo;
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded">&rsaquo;</span>
    <?php endif; ?>

    <!-- Trang cuối -->
    <?php if ($currentPage < $totalPages): ?>
    <a href="?page=<?= $totalPages ?>&limit=<?= $limit ?>"
        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
        &raquo;
    </a>
    <?php else: ?>
    <span class="px-3 py-1 bg-gray-200 text-gray-400 rounded">&raquo;</span>
    <?php endif; ?>
</nav>
<?php
}
?>