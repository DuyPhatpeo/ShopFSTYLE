<!DOCTYPE html>
<html lang="vi">
<?php include ('link.php'); ?>

<body class="bg-[#f5f7ff] min-h-screen">
    <!-- Overlay khi mở menu trên mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-30"></div>

    <div class="flex">
        <?php include ('sidebar.php'); ?>
        <!-- Gọi sidebar -->
        <div class="flex-1 flex flex-col min-h-screen">
            <?php include ('navbar.php'); ?>
            <!-- Gọi navbar -->