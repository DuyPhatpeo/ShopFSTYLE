<!DOCTYPE html>
<html lang="vi">
<?php include ('link.php'); ?>

<body class="bg-[#f5f7ff] min-h-screen">
    <!-- Overlay khi mở menu trên mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-30"></div>

    <div class="flex min-h-screen">
        <!-- Gọi sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Khối nội dung chính, chừa chỗ cho sidebar khi >= lg -->
        <div class="flex-1 ml-0 lg:ml-72">
            <!-- Gọi navbar -->
            <?php include('navbar.php'); ?>

            <!-- Nội dung trang chính -->
            <div class="mt-20">