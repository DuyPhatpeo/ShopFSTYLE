<?php
include("../../includes/header.php");
?>

<main>
    <div class="container mx-auto p-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Thêm thương hiệu</h1>
            <form action="store-brand.php" method="POST" class="space-y-4">
                <!-- Tên thương hiệu -->
                <div>
                    <label for="brand_name" class="block text-gray-700 font-medium">Tên thương hiệu</label>
                    <input type="text" id="brand_name" name="brand_name" placeholder="Nhập tên thương hiệu" required
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-gray-700 font-medium">Trạng thái</label>
                    <select id="status" name="status"
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="1">On</option>
                        <option value="2">Off</option>
                    </select>
                </div>
                <!-- Nút submit và quay lại -->
                <div class="flex items-center justify-between">
                    <a href="../brand/index.php" class="text-blue-600 hover:text-blue-800">Quay lại</a>
                    <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                        <!-- Icon SVG cho nút thêm -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Thêm thương hiệu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>


<?php 
include('../../includes/footer.php'); 
?>