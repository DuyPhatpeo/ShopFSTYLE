<?php
require_once('../../model/dashboardModel.php');

// Lấy filter từ URL, mặc định là 'month'
$filter = $_GET['filter'] ?? 'month';

// Lấy dữ liệu tổng quan
$data = [
    'totalOrders'    => getTotalOrders($conn),
    'totalCustomers' => getTotalCustomers($conn),
    'totalProducts'  => getTotalProducts($conn),
    'totalRevenue'   => getTotalRevenue($conn),
    'recentOrders'   => getRecentOrders($conn),
    'filter'         => $filter // lưu lại filter hiện tại để view xử lý chọn option
];

// Lấy doanh thu theo filter
switch ($filter) {
    case 'day':
        $data['revenueData'] = getDailyRevenue($conn);
        break;
    case 'quarter':
        $data['revenueData'] = getQuarterlyRevenue($conn);
        break;
    case 'month':
    default:
        $data['revenueData'] = getMonthlyRevenue($conn);
        break;
}

// Trả về mảng data để view sử dụng
return $data;