<?php
require_once('../../model/dashboardModel.php');

$data = [
    'totalOrders' => getTotalOrders($conn),
    'totalCustomers' => getTotalCustomers($conn),
    'totalProducts' => getTotalProducts($conn),
    'totalRevenue' => getTotalRevenue($conn),
    'recentOrders' => getRecentOrders($conn),
    'monthlyRevenue' => getMonthlyRevenue($conn) // nếu bạn có thêm function này
];

// ➡️ Thêm dòng dưới để `$data` có thể dùng ngoài view
return $data;
?>