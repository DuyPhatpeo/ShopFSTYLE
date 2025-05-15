<?php
// File: admin/model/dashboardModel.php

function getTotalOrders($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM `order`");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function getTotalCustomers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function getTotalProducts($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM product");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function getTotalRevenue($conn) {
    $stmt = $conn->prepare("SELECT SUM(total_amount) as revenue FROM `order` WHERE status = 'completed'");
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['revenue'] ?? 0;
}

function getRecentOrders($conn, $limit = 5) {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.total_amount, o.status, o.created_at, c.full_name 
        FROM `order` o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        ORDER BY o.created_at DESC
        LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Doanh thu theo ngày
function getDailyRevenue($conn) {
    $revenue = [];
    $stmt = $conn->prepare("
        SELECT 
            DATE(created_at) AS day,
            SUM(total_amount) as total
        FROM `order`
        WHERE status = 'completed'
        GROUP BY day
        ORDER BY day ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $revenue[$row['day']] = (int)$row['total'];
    }
    return $revenue;
}

// Doanh thu theo tháng
function getMonthlyRevenue($conn) {
    $revenue = [];
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            SUM(total_amount) as total
        FROM `order`
        WHERE status = 'completed'
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $revenue[$row['month']] = (int)$row['total'];
    }
    return $revenue;
}

// Doanh thu theo quý
function getQuarterlyRevenue($conn) {
    $revenue = [];
    $stmt = $conn->prepare("
        SELECT 
            CONCAT(YEAR(created_at), '-Q', QUARTER(created_at)) AS quarter,
            SUM(total_amount) as total
        FROM `order`
        WHERE status = 'completed'
        GROUP BY quarter
        ORDER BY quarter ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $revenue[$row['quarter']] = (int)$row['total'];
    }
    return $revenue;
}
?>