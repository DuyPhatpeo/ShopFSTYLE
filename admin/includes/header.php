<!DOCTYPE html>
<html lang="vi">
<?php include ('includes/link.php'); ?>

<body class="bg-[#f5f7ff] min-h-screen">
    <!-- Overlay khi mở menu trên mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-30"></div>

    <div class="flex">
        <?php include ('includes/sidebar.php'); ?>
        <!-- Gọi sidebar -->
        <div class="flex-1 flex flex-col min-h-screen">
            <?php include ('includes/navbar.php'); ?>
            <!-- Gọi navbar -->