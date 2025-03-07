<!-- Search Modal: Đổi màu overlay và màu nền modal -->
<div id="search-modal" class="fixed inset-0 z-50 flex items-start justify-center bg-gray-700 bg-opacity-50 
           opacity-0 pointer-events-none transition-opacity duration-300">
    <!-- Modal content: sử dụng bg-gray-50 và mở rộng modal với max-w-3xl -->
    <div class="mt-10 bg-gray-50 w-11/12 max-w-3xl p-6 rounded-lg shadow-lg transform -translate-y-full 
                transition-transform duration-300">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Tìm kiếm</h2>
            <button id="close-search-modal" class="text-gray-600 hover:text-black focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <!-- Input tìm kiếm -->
        <input type="text" placeholder="Nhập từ khóa tìm kiếm..." class="w-full px-4 py-2 border border-gray-300 rounded-full 
                   focus:outline-none focus:border-gray-500" />
        <!-- Ô hiển thị danh sách sản phẩm tìm kiếm -->
        <div class="mt-4 bg-white rounded shadow-lg p-4 max-h-60 overflow-y-auto">
            <ul class="divide-y divide-gray-200">
                <li class="py-2">Sản phẩm 1</li>
                <li class="py-2">Sản phẩm 2</li>
                <li class="py-2">Sản phẩm 3</li>
                <!-- Thêm các mục sản phẩm khác nếu cần -->
            </ul>
        </div>
    </div>
</div>