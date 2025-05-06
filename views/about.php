<?php 
    include ('../includes/header.php'); 
    include ('../includes/search.php');  
?>


<!-- Giới thiệu về Shop FStyle -->
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Về Chúng Tôi - FStyle</h1>

    <div class="text-gray-700 leading-relaxed text-justify text-lg space-y-6">
        <p>
            <strong>FStyle</strong> là cửa hàng thời trang trực tuyến mang phong cách trẻ trung, hiện đại và thời
            thượng.
            Chúng tôi cam kết mang đến cho khách hàng những sản phẩm chất lượng, mẫu mã độc đáo và luôn bắt kịp xu hướng
            mới nhất.
        </p>

        <p>
            Với tiêu chí <strong>"Chất lượng là uy tín – Khách hàng là trung tâm"</strong>, FStyle không ngừng nỗ lực để
            cải tiến trải nghiệm mua sắm,
            từ giao diện dễ dùng đến dịch vụ chăm sóc khách hàng tận tâm. Bạn có thể dễ dàng tìm kiếm và lựa chọn những
            món đồ phù hợp với phong cách của riêng mình.
        </p>

        <p>
            Chúng tôi cung cấp đa dạng sản phẩm từ áo, quần, giày dép đến phụ kiện thời trang. Dù bạn là học sinh, sinh
            viên hay dân văn phòng,
            FStyle đều có những lựa chọn phù hợp để bạn thể hiện cá tính riêng.
        </p>

        <p>
            Hãy đồng hành cùng <strong>FStyle</strong> để tự tin thể hiện bản thân mỗi ngày – vì thời trang không chỉ là
            quần áo, mà còn là cá tính.
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-blue-100 p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2 text-blue-800">🌟 Sứ mệnh</h2>
            <p class="text-gray-700">
                Đem đến trải nghiệm thời trang toàn diện, tiện lợi và hợp thời cho giới trẻ Việt.
            </p>
        </div>
        <div class="bg-green-100 p-6 rounded-xl shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2 text-green-800">🎯 Tầm nhìn</h2>
            <p class="text-gray-700">
                Trở thành một trong những nền tảng thời trang trực tuyến hàng đầu Việt Nam.
            </p>
        </div>
    </div>

    <div class="mt-10 text-center">
        <a href="<?= USER_URL ?>"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-full transition duration-300">
            Khám phá sản phẩm
        </a>
    </div>
</div>

<?php include ('../includes/footer.php'); ?>