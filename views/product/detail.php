<!-- Thêm vào phần thông tin sản phẩm -->
<div class="flex items-center space-x-4">
    <button id="favouriteBtn" class="flex items-center space-x-2 text-gray-600 hover:text-red-500">
        <span id="favouriteText">Yêu thích</span>
    </button>
</div>

<!-- Thêm script xử lý yêu thích -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favouriteBtn = document.getElementById('favouriteBtn');
    const heartIcon = document.getElementById('heartIcon');
    const favouriteText = document.getElementById('favouriteText');
    const productId = <?= $product['product_id'] ?>;

    // Kiểm tra trạng thái yêu thích ban đầu
    checkFavouriteStatus();

    // Xử lý sự kiện click nút yêu thích
    favouriteBtn.addEventListener('click', function() {
        const formData = new FormData();
        formData.append('action', heartIcon.classList.contains('text-red-500') ? 'remove' : 'add');
        formData.append('product_id', productId);

        fetch('<?= BASE_URL ?>/controller/favouriteController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toggleFavouriteStatus();
                showNotification(data.message);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra', 'error');
        });
    });

    // Hàm kiểm tra trạng thái yêu thích
    function checkFavouriteStatus() {
        const formData = new FormData();
        formData.append('action', 'check');
        formData.append('product_id', productId);

        fetch('<?= BASE_URL ?>/controller/favouriteController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.isFavourite) {
                heartIcon.classList.add('text-red-500');
                favouriteText.textContent = 'Đã yêu thích';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Hàm chuyển đổi trạng thái yêu thích
    function toggleFavouriteStatus() {
        if (heartIcon.classList.contains('text-red-500')) {
            heartIcon.classList.remove('text-red-500');
            favouriteText.textContent = 'Yêu thích';
        } else {
            heartIcon.classList.add('text-red-500');
            favouriteText.textContent = 'Đã yêu thích';
        }
    }

    // Hàm hiển thị thông báo
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script> 