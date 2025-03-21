<?php
    // $error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin - FSTYLE</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/images/logo-fstyle.ico">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-r from-[#7165FF] to-[#5e53e8]">
    <!-- Nội dung chính -->
    <main class="flex-grow flex items-center justify-center">
        <!-- Form đăng nhập -->
        <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
            <!-- Logo ở trên cùng form -->
            <div class="flex justify-center mb-4">
                <img src="../../assets/images/logo-fstyle.ico" alt="Logo" class="h-16 w-auto">
            </div>

            <h2 class="text-2xl font-bold text-center text-[#7165FF] mb-4">Đăng nhập Admin</h2>


            <form action="?controller=account&action=loginProcess" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium">Tài khoản</label>
                    <input type="text" id="username" name="username" required
                        oninvalid="this.setCustomValidity('Vui lòng nhập tài khoản!')"
                        oninput="this.setCustomValidity('')"
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#7165FF]">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium">Mật khẩu</label>
                    <input type="password" id="password" name="password" required
                        oninvalid="this.setCustomValidity('Vui lòng nhập mật khẩu!')"
                        oninput="this.setCustomValidity('')"
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#7165FF]">
                </div>

                <!-- Nút đăng nhập dạng gradient -->
                <button type="submit"
                    class="w-full text-white py-2 rounded bg-gradient-to-r from-[#7165FF] to-[#5e53e8] hover:opacity-90 transition">
                    Đăng nhập
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-sm text-white py-2">
        © 2025 FSTYLE ADMIN. Được phát triển bởi Trần Duy Phát.
    </footer>
</body>

</html>