<!-- Mobile Search Overlay (chỉ hiển thị trên mobile) -->
<div id="mobile-search"
    class="md:hidden fixed top-20 left-0 w-full bg-white shadow-lg transform -translate-y-full transition-transform duration-300 z-40">
    <div class="flex items-center p-4">
        <input type="text" placeholder="Tìm kiếm..."
            class="flex-grow px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-gray-500" />
        <button id="close-search" class="ml-4 text-gray-600 hover:text-black">
            <img src="assets/icons/close.svg" alt="close" class="w-6 h-6" />
        </button>
    </div>
</div>