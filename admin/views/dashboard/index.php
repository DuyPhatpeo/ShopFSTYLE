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

        <!-- Biểu đồ doanh thu -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-10">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Doanh thu theo tháng</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <!-- Đơn hàng mới nhất -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Đơn hàng mới nhất</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-3">Mã đơn</th>
                            <th class="px-6 py-3">Khách hàng</th>
                            <th class="px-6 py-3">Tổng tiền</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $data['recentOrders']->fetch_assoc()) : ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4"><?= htmlspecialchars($order['order_id']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($order['full_name']) ?></td>
                            <td class="px-6 py-4"><?= number_format($order['total_amount']) ?> đ</td>

                            <!-- Hiển thị trạng thái với màu sắc -->
                            <td class="px-6 py-4">
                                <?php
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded">Chờ xác nhận</span>';
                                            break;
                                        case 'processing':
                                            echo '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded">Đang xử lý</span>';
                                            break;
                                        case 'shipping':
                                            echo '<span class="px-2 py-1 bg-indigo-200 text-indigo-800 rounded">Đang giao</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="px-2 py-1 bg-green-200 text-green-800 rounded">Hoàn thành</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="px-2 py-1 bg-red-200 text-red-800 rounded">Đã huỷ</span>';
                                            break;
                                        default:
                                            echo '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Unknown</span>';
                                            break;
                                    }
                                ?>
                            </td>

                            <td class="px-6 py-4"><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($data['monthlyRevenue'])) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_values($data['monthlyRevenue'])) ?>,
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
</script>

<?php include("../../includes/footer.php"); ?>