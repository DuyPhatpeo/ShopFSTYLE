<!-- ======================== START: Category List Section ======================== -->
<section class="w-full py-10 relative">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">DANH MỤC SẢN PHẨM</h2>
        </div>

        <!-- Swiper Slider for Categories -->
        <div class="swiper categorySwiper">
            <div class="swiper-wrapper">
                <!-- Card Danh mục 1 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category1.png" alt="Danh mục 1"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 1
                    </p>
                </div>
                <!-- Card Danh mục 2 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category2.png" alt="Danh mục 2"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 2
                    </p>
                </div>
                <!-- Card Danh mục 3 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category3.png" alt="Danh mục 3"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 3
                    </p>
                </div>
                <!-- Card Danh mục 4 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category4.png" alt="Danh mục 4"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 4
                    </p>
                </div>
                <!-- Card Danh mục 5 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category5.png" alt="Danh mục 5"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 5
                    </p>
                </div>
                <!-- Card Danh mục 6 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category6.png" alt="Danh mục 6"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 6
                    </p>
                </div>
                <!-- Card Danh mục 7 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category7.png" alt="Danh mục 7"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 7
                    </p>
                </div>
                <!-- Card Danh mục 8 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category8.png" alt="Danh mục 8"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 8
                    </p>
                </div>
                <!-- Card Danh mục 9 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category1.png" alt="Danh mục 9"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 9
                    </p>
                </div>
                <!-- Card Danh mục 10 -->
                <div class="swiper-slide flex flex-col items-center cursor-pointer"
                    onclick="location.href='views/category/category-detail.php'">
                    <img src="assets/images/public/category/category2.png" alt="Danh mục 10"
                        class="w-full h-[350px] object-cover rounded-lg mb-3" />
                    <p class="text-lg font-semibold hover:text-blue-500 transition-colors duration-200">
                        Danh mục 10
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======================== END: Category List Section ======================== -->

<!-- SwiperJS Initialization -->
<script>
var swiper = new Swiper(".categorySwiper", {
    slidesPerView: 2,
    spaceBetween: 15,
    loop: true,
    slidesPerGroup: 1, // Kéo từng ảnh 1 qua
    breakpoints: {
        640: {
            slidesPerView: 2,
            slidesPerGroup: 1
        },
        768: {
            slidesPerView: 3,
            slidesPerGroup: 1
        },
        1024: {
            slidesPerView: 5,
            slidesPerGroup: 1
        },
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
</script>