<!-- Banner Slider Section -->
<section class="relative w-full h-screen">
    <div class="swiper mySwiper w-full h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide relative">
                <img src="<?= USER_URL ?>/assets/images/public/banner/banner1.png" alt="Banner 1"
                    class="w-full h-full object-cover" />
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide relative">
                <img src="<?= USER_URL ?>/assets/images/public/banner/banner2.png" alt="Banner 1"
                    class="w-full h-full object-cover" />
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide relative">
                <img src="<?= USER_URL ?>/assets/images/public/banner/banner1.png" alt="Banner 1"
                    class="w-full h-full object-cover" />
            </div>
            <div class="swiper-slide relative">
                <img src="<?= USER_URL ?>/assets/images/public/banner/banner2.png" alt="Banner 1"
                    class="w-full h-full object-cover" />
            </div>
        </div>
        <!-- Pagination & Navigation -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next text-white"></div>
        <div class="swiper-button-prev text-white"></div>
    </div>
</section>

<!-- SwiperJS Initialization -->
<script>
var swiper = new Swiper(".mySwiper", {
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
</script>