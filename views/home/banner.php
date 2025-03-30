<section class="relative w-full h-screen">
    <div class="swiper mySwiper w-full h-full">
        <div class="swiper-wrapper" id="bannerContainer"></div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-next text-white"></div>
        <div class="swiper-button-prev text-white"></div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch("<?= USER_URL ?>/controller/bannerController.php")
        .then(response => response.json())
        .then(data => {
            let bannerHTML = "";
            data.forEach(banner => {
                bannerHTML += `
                    <div class="swiper-slide">
                        <img src="<?= USER_URL ?>/${banner.image_url}" alt="Banner" class="w-full h-full object-cover" />
                    </div>
                `;
            });
            document.getElementById("bannerContainer").innerHTML = bannerHTML;

            // Khởi tạo Swiper sau khi load ảnh
            new Swiper(".mySwiper", {
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                },
            });
        });
});
</script>