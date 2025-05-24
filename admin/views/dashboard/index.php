<?php
$pageTitle = "Dashboard";
include("../../includes/session_check.php");
include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/dashboardController.php'); // Controller

?>

<main>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-8">Bảng điều khiển</h1>

        <!-- Tổng quan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                <h2 class="text-gray-600 text-sm uppercase">Tổng đơn hàng</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-2"><?= $data['totalOrders'] ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                <h2 class="text-gray-600 text-sm uppercase">Tổng khách hàng</h2>
                <p class="text-2xl font-bold text-green-600 mt-2"><?= $data['totalCustomers'] ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                <h2 class="text-gray-600 text-sm uppercase">Tổng sản phẩm</h2>
                <p class="text-2xl font-bold text-yellow-600 mt-2"><?= $data['totalProducts'] ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                <h2 class="text-gray-600 text-sm uppercase">Tổng doanh thu</h2>
                <p class="text-2xl font-bold text-red-600 mt-2"><?= number_format($data['totalRevenue']) ?> đ</p>
            </div>
        </div>

        <!-- Filter chọn loại doanh thu -->
        <div class="mb-4">
            <label for="revenueFilter" class="block text-gray-700 font-semibold mb-2">Xem doanh thu theo:</label>
            <select id="revenueFilter" class="border rounded px-3 py-2">
                <option value="day" <?= ($data['filter'] === 'day') ? 'selected' : '' ?>>Ngày</option>
                <option value="month" <?= ($data['filter'] === 'month') ? 'selected' : '' ?>>Tháng</option>
                <option value="quarter" <?= ($data['filter'] === 'quarter') ? 'selected' : '' ?>>Quý</option>
            </select>
        </div>

        <!-- Biểu đồ doanh thu -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-10">
            <h2 class="text-xl font-semibold mb-4 text-gray-700" id="chartTitle">
                Doanh thu theo <?= ucfirst($data['filter']) ?>
            </h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Lấy dữ liệu doanh thu từ PHP
const revenueData = <?= json_encode($data['revenueData']) ?>;
const filter = '<?= $data['filter'] ?>';

// Chuẩn bị labels và data cho biểu đồ
const labels = Object.keys(revenueData);
const dataValues = Object.values(revenueData);

// Thay đổi tiêu đề theo filter
const titleMap = {
    day: 'Doanh thu theo ngày',
    month: 'Doanh thu theo tháng',
    quarter: 'Doanh thu theo quý'
};

const ctx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: dataValues,
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgb(99, 102, 241)',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' đ';
                    }
                }
            }
        }
    }
});

// Xử lý thay đổi filter
document.getElementById('revenueFilter').addEventListener('change', function() {
    const selectedFilter = this.value;

    // Chuyển trang với filter mới (reload page)
    const url = new URL(window.location.href);
    url.searchParams.set('filter', selectedFilter);
    window.location.href = url.toString();
});

// Cập nhật tiêu đề
document.getElementById('chartTitle').textContent = titleMap[filter] || 'Doanh thu theo tháng';
</script>


<?php include("../../includes/footer.php"); ?>