<?php 
    include ('../includes/header.php'); 
    include ('../includes/search.php');  
?>

<!-- Trang Liên hệ -->
<div class="container mx-auto px-4 py-12 max-w-6xl text-gray-800">

    <!-- Tiêu đề -->
    <h1 class="text-4xl font-bold text-center mb-10">Liên hệ với <span class="text-blue-600">FStyle</span></h1>

    <!-- Thông tin liên hệ -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white p-8 rounded-2xl shadow-lg">
        <div class="space-y-6 text-lg">

            <div class="flex gap-4 items-start">
                <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1 1 0 01-1.414 0l-4.243-4.243M3.515 12.9a9 9 0 1112.728 0" />
                </svg>
                <p><strong>Địa chỉ:</strong> 123 Đường Thời Trang, Quận Style, TP. Hồ Chí Minh</p>
            </div>

            <div class="flex gap-4 items-start">
                <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5h2l.4 2M7 13h10l4-8H5.4M7 13l-2 5h12l-2-5m-4 0v6" />
                </svg>
                <p><strong>Hotline:</strong> <a href="tel:0123456789" class="text-blue-600 hover:underline">0123 456
                        789</a></p>
            </div>

            <div class="flex gap-4 items-start">
                <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12H8m0 0l-4-4m4 4l-4 4m16-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p><strong>Email:</strong> <a href="mailto:contact@fstyle.vn"
                        class="text-blue-600 hover:underline">contact@fstyle.vn</a></p>
            </div>

            <div class="flex gap-4 items-start">
                <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p><strong>Giờ làm việc:</strong> 8h – 21h (Thứ 2 đến Chủ Nhật)</p>
            </div>

            <div class="flex gap-4 items-start">
                <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636l-1.414 1.414a2 2 0 01-2.828 0L12 5.828l-2.121 2.122a2 2 0 01-2.828 0L5.636 5.636a9 9 0 1012.728 0z" />
                </svg>
                <p><strong>Fanpage:</strong> <a href="#"
                        class="text-blue-600 hover:underline">facebook.com/fstyle.vn</a></p>
            </div>
        </div>

        <!-- Google Map -->
        <div class="rounded-xl overflow-hidden shadow-md">
            <iframe class="w-full h-80"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.475601745595!2d106.70042431428713!3d10.776374792322014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3a0dbaf1ed%3A0x9a4993f4f29f19fa!2zUGFya2NvIFRvd2VyIC0gU8O0biBWaWV0bmFt!5e0!3m2!1svi!2s!4v1626417744471!5m2!1svi!2s"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <!-- Tại sao chọn FStyle -->
    <div class="mt-16 bg-blue-50 rounded-2xl p-8 shadow-md">
        <h2 class="text-2xl font-semibold text-center mb-6">🌟 Tại sao nên chọn <span
                class="text-blue-600">FStyle?</span></h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center text-gray-700">
            <div class="space-y-2">
                <div class="text-4xl">🚚</div>
                <p class="font-medium">Giao hàng nhanh toàn quốc</p>
            </div>
            <div class="space-y-2">
                <div class="text-4xl">✅</div>
                <p class="font-medium">Đổi trả dễ dàng trong 7 ngày</p>
            </div>
            <div class="space-y-2">
                <div class="text-4xl">💬</div>
                <p class="font-medium">Tư vấn chuyên nghiệp, nhiệt tình</p>
            </div>
        </div>
    </div>
</div>

<?php include ('../includes/footer.php'); ?>