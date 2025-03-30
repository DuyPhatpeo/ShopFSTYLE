<?php
class BannerController {
    private $bannerPath = "../admin/uploads/banners/";

    // Lấy danh sách banner từ thư mục
    public function getBanners() {
        $banners = [];
        $files = glob($this->bannerPath . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

        foreach ($files as $file) {
            $banners[] = [
                "image_url" => str_replace("../", "", $file) // Loại bỏ dấu `../` để đúng đường dẫn
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($banners);
    }
}

// Xử lý request
$bannerController = new BannerController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $bannerController->getBanners();
}
?>