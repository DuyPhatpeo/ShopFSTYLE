<?php
// File: admin/controller/productController.php

/*==================*
 *  Utility Functions
 *==================*/

/**
 * Sinh UUID v4 đơn giản.
 * Dùng cho sản phẩm, biến thể, ảnh,...
 *
 * @return string UUID v4.
 */
function generateUUID() {
    $data = random_bytes(16);
    // Version 4: đặt bit phiên bản
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Variant: đặt bit biến thể (10xxxxxx)
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf(
        '%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

/**
 * Kiểm tra xem tên sản phẩm đã tồn tại hay chưa.
 * Nếu cập nhật (với $excludeId) thì loại trừ sản phẩm đó.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $productName Tên sản phẩm.
 * @param string|null $excludeId ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isProductNameExists($conn, $productName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM product WHERE product_name = ? AND product_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $productName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM product WHERE product_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/*==================*
 *  STEP 1: Add Product
 *==================*/

/**
 * Xử lý thêm sản phẩm (Bước 1).
 * Các trường cần: product_name, brand_id, category_id, original_price, discount_price, descriptionProduct.
 *
 * @param mysqli $conn      Kết nối CSDL.
 * @param array  $postData  Dữ liệu form (POST).
 * @param array  $fileData  Dữ liệu file upload.
 * @param array  &$errors   Mảng lỗi (nếu có).
 *
 * @return string|null      Trả về product_id nếu thành công, ngược lại trả về null.
 */
function processAddProductStep1($conn, $postData, $fileData, &$errors) {
    $productName   = trim($postData['product_name'] ?? '');
    $description   = trim($postData['descriptionProduct'] ?? '');
    $originalPrice = trim($postData['original_price'] ?? '');
    $discountPrice = trim($postData['discount_price'] ?? '');
    $brandId       = trim($postData['brand_id'] ?? '');
    $categoryId    = trim($postData['category_id'] ?? '');

    if (empty($productName)) {
        $errors['product_name'] = "Tên sản phẩm không được để trống.";
    } elseif (isProductNameExists($conn, $productName)) {
        $errors['product_name'] = "Tên sản phẩm đã tồn tại.";
    }
    if (empty($brandId)) {
        $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
    } else {
        $checkSql = "SELECT brand_id FROM brand WHERE brand_id = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("s", $brandId);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows === 0) {
            $errors['brand_id'] = "Thương hiệu này không tồn tại.";
        }
    }
    if (empty($categoryId)) {
        $errors['category_id'] = "Vui lòng chọn danh mục.";
    } else {
        $checkSql = "SELECT category_id FROM category WHERE category_id = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("s", $categoryId);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows === 0) {
            $errors['category_id'] = "Danh mục này không tồn tại.";
        }
    }
    if (empty($originalPrice) || !is_numeric($originalPrice) || (float)$originalPrice <= 0) {
        $errors['original_price'] = "Giá gốc phải là số > 0.";
    }
    if ($discountPrice === '') {
        $discountPrice = 0;
    } elseif (!is_numeric($discountPrice) || (float)$discountPrice < 0) {
        $errors['discount_price'] = "Giá khuyến mãi phải là số >= 0.";
    }
    if (!empty($errors)) {
        return null;
    }
    $originalPrice = (float)$originalPrice;
    $discountPrice = (float)$discountPrice;
    $status    = "1";
    $createdBy = "1"; // Ví dụ: admin_id = "1"
    $product_id = generateUUID();

    $sql = "INSERT INTO product (
                product_id, product_name, description, original_price, discount_price,
                brand_id, category_id, created_by, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddssss",
        $product_id,
        $productName,
        $description,
        $originalPrice,
        $discountPrice,
        $brandId,
        $categoryId,
        $createdBy,
        $status
    );
    if (!$stmt->execute()) {
        $errors['general'] = "Không thể thêm sản phẩm: " . $conn->error;
        return null;
    }
    return $product_id;
}

/*==================*
 *  STEP 2: Add Variants
 *==================*/

/**
 * Xử lý thêm biến thể cho sản phẩm (Bước 2).
 * Nếu có dữ liệu từ form về biến thể (variant_color, variant_size, variant_quantity)
 * thì tạo từng biến thể; nếu không có, dùng số lượng chung (default_quantity) để tạo biến thể mặc định.
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm vừa tạo.
 * @param array  $postData   Dữ liệu form (POST).
 * @param array  $fileData   Dữ liệu file (nếu có).
 * @param array  &$errors    Mảng lỗi (nếu có).
 */
function processAddVariantsStep2($conn, $product_id, $postData, $fileData, &$errors) {
    $colors     = $postData['variant_color'] ?? [];
    $sizes      = $postData['variant_size'] ?? [];
    $quantities = $postData['variant_quantity'] ?? [];
    
    if (!empty($colors) && !empty($sizes) && !empty($quantities)) {
        $count = count($colors);
        for ($i = 0; $i < $count; $i++) {
            $colorId = trim($colors[$i] ?? '');
            $sizeId  = trim($sizes[$i] ?? '');
            $qty     = trim($quantities[$i] ?? '');
            if (empty($colorId)) {
                $errors['variants'] = "Biến thể thứ " . ($i+1) . " bắt buộc phải có màu.";
                continue;
            }
            // Với kích cỡ, nếu không có, sẽ chuyển thành null ở hàm addProductVariant()
            if (!is_numeric($qty) || (int)$qty < 0) {
                $errors['variants'] = "Biến thể thứ " . ($i+1) . " số lượng phải >= 0.";
                continue;
            }
            addProductVariant($conn, $product_id, [
                'color_id' => $colorId,
                'size_id'  => $sizeId,
                'quantity' => (int)$qty,
                'status'   => "1"
            ]);
        }
    } else {
        // Nếu không có dữ liệu biến thể cụ thể, tạo biến thể mặc định với số lượng chung.
        $defaultQuantity = trim($postData['default_quantity'] ?? '');
        if ($defaultQuantity === '' || !is_numeric($defaultQuantity) || (int)$defaultQuantity < 0) {
            $errors['default_quantity'] = "Số lượng chung phải là số >= 0.";
            return;
        }
        // Vì màu là bắt buộc, nếu không có biến thể riêng, có thể thông báo lỗi hoặc bắt buộc chọn màu.
        // Ở đây, ta cho rằng trường hợp này không xảy ra và chuyển giá trị màu và kích cỡ thành null.
        addProductVariant($conn, $product_id, [
            'color_id' => null, // Nếu không có, bạn có thể yêu cầu bắt buộc chọn màu.
            'size_id'  => null,
            'quantity' => (int)$defaultQuantity,
            'status'   => "1"
        ]);
    }
}

/**
 * Thêm một biến thể vào bảng product_variants.
 *
 * @param mysqli $conn        Kết nối CSDL.
 * @param string $product_id  ID sản phẩm.
 * @param array  $variantData Dữ liệu biến thể gồm: color_id, size_id, quantity, status.
 *
 * @return string|null        Trả về variant_id nếu thành công, null nếu thất bại.
 */
function addProductVariant($conn, $product_id, $variantData) {
    // Bắt buộc phải có màu
    if (empty($variantData['color_id'])) {
        return null;
    }
    
    $variant_id = generateUUID();
    $status     = $variantData['status'] ?? "1";
    $color_id   = $variantData['color_id'];
    // Nếu không có kích cỡ, chuyển thành null
    $size_id    = !empty($variantData['size_id']) ? $variantData['size_id'] : null;
    $quantity   = (int)($variantData['quantity'] ?? 0);
    
    $sql = "INSERT INTO product_variants (
                variant_id, product_id, color_id, size_id, quantity, status
            ) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis",
        $variant_id,
        $product_id,
        $color_id,
        $size_id,
        $quantity,
        $status
    );
    if ($stmt->execute()) {
        return $variant_id;
    }
    return null;
}

/*==================*
 *  STEP 3: Add Variant Images
 *==================*/

/**
 * Xử lý upload ảnh cho biến thể (Bước 3).
 * Dữ liệu file được gửi theo dạng: variant_images[variant_id][].
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm.
 * @param array  $postData   Dữ liệu form (nếu cần).
 * @param array  $fileData   Dữ liệu file upload.
 * @param array  &$errors    Mảng lỗi (nếu có).
 */
function processAddVariantImagesStep3($conn, $product_id, $postData, $fileData, &$errors) {
    $sql = "SELECT variant_id FROM product_variants WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $variant_id = $row['variant_id'];
        if (isset($fileData['variant_images']['name'][$variant_id])) {
            $files = [];
            foreach ($fileData['variant_images']['name'][$variant_id] as $k => $v) {
                $files[] = [
                    'name'     => $fileData['variant_images']['name'][$variant_id][$k],
                    'type'     => $fileData['variant_images']['type'][$variant_id][$k],
                    'tmp_name' => $fileData['variant_images']['tmp_name'][$variant_id][$k],
                    'error'    => $fileData['variant_images']['error'][$variant_id][$k],
                    'size'     => $fileData['variant_images']['size'][$variant_id][$k],
                ];
            }
            uploadVariantImages($conn, $variant_id, $files);
        }
    }
}

/**
 * Upload nhiều ảnh cho một biến thể.
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $variant_id ID biến thể.
 * @param array  $files      Mảng file cần upload.
 *
 * @return array             Mảng chứa image_ids đã upload.
 */
function uploadVariantImages($conn, $variant_id, $files) {
    $imageIds = [];
    foreach ($files as $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../uploads/variants/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename   = 'variant_' . uniqid() . '.' . $extension;
            $targetFile = $targetDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $imagePath = 'admin/uploads/variants/' . $filename;
                $image_id  = addImage($conn, $imagePath, "");
                if ($image_id) {
                    addVariantImage($conn, $variant_id, $image_id);
                    $imageIds[] = $image_id;
                }
            }
        }
    }
    return $imageIds;
}

/**
 * Thêm ảnh vào bảng images.
 *
 * @param mysqli $conn    Kết nối CSDL.
 * @param string $path    Đường dẫn ảnh.
 * @param string $caption Chú thích (nếu có).
 *
 * @return string|null    Trả về image_id nếu thành công, null nếu thất bại.
 */
function addImage($conn, $path, $caption = "") {
    $image_id = generateUUID();
    $sql = "INSERT INTO images (image_id, image_url, caption) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $image_id, $path, $caption);
    if ($stmt->execute()) {
        return $image_id;
    }
    return null;
}

/**
 * Liên kết ảnh với biến thể trong bảng variant_images.
 *
 * @param mysqli $conn      Kết nối CSDL.
 * @param string $variant_id ID biến thể.
 * @param string $image_id   ID ảnh.
 *
 * @return bool             True nếu thành công, false nếu thất bại.
 */
function addVariantImage($conn, $variant_id, $image_id) {
    $sql = "INSERT INTO variant_images (variant_id, image_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $variant_id, $image_id);
    return $stmt->execute();
}

/*==================*
 *  Cập nhật số lượng
 *==================*/

/**
 * Cập nhật số lượng của biến thể sản phẩm.
 * Hàm này cộng thêm số lượng vào số lượng hiện tại của biến thể.
 *
 * @param mysqli $conn               Kết nối CSDL.
 * @param string $variant_id         ID biến thể cần cập nhật.
 * @param int    $additionalQuantity Số lượng cần thêm.
 *
 * @return bool True nếu cập nhật thành công, false nếu thất bại.
 */
function updateVariantQuantity($conn, $variant_id, $additionalQuantity) {
    $additionalQuantity = (int)$additionalQuantity;
    if ($additionalQuantity <= 0) {
        return false;
    }
    
    $sql = "UPDATE product_variants SET quantity = quantity + ? WHERE variant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $additionalQuantity, $variant_id);
    return $stmt->execute();
}

/*==================*
 *  Dropdown & Pagination, Lấy dữ liệu
 *==================*/

/**
 * Lấy thông tin chi tiết của sản phẩm theo product_id.
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm.
 *
 * @return array|null        Trả về mảng dữ liệu sản phẩm hoặc null nếu không tồn tại.
 */
function getProductById($conn, $product_id) {
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Lấy danh sách tất cả thương hiệu (cho dropdown).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array       Trả về mảng danh sách thương hiệu.
 */
function getAllBrands($conn) {
    $sql = "SELECT brand_id, brand_name FROM brand WHERE status = 1 ORDER BY brand_name ASC";
    $result = $conn->query($sql);
    $brands = [];
    while ($row = $result->fetch_assoc()){
        $brands[] = $row;
    }
    return $brands;
}

/**
 * Lấy danh sách tất cả danh mục (cho dropdown).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array       Trả về mảng danh sách danh mục.
 */
function getAllCategories($conn) {
    $sql = "SELECT category_id, category_name FROM category WHERE status = 1 ORDER BY category_name ASC";
    $result = $conn->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()){
        $categories[] = $row;
    }
    return $categories;
}

/**
 * Lấy danh sách tất cả màu (cho dropdown).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array       Trả về mảng danh sách màu.
 */
function getAllColors($conn) {
    $sql = "SELECT color_id, color_name, color_code FROM colors ORDER BY color_name ASC";
    $result = $conn->query($sql);
    $colors = [];
    while ($row = $result->fetch_assoc()){
        $colors[] = $row;
    }
    return $colors;
}

/**
 * Lấy danh sách tất cả kích cỡ (cho dropdown).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array       Trả về mảng danh sách kích cỡ.
 */
function getAllSizes($conn) {
    $sql = "SELECT size_id, size_name FROM sizes ORDER BY size_name ASC";
    $result = $conn->query($sql);
    $sizes = [];
    while ($row = $result->fetch_assoc()){
        $sizes[] = $row;
    }
    return $sizes;
}

/**
 * Lấy danh sách sản phẩm có phân trang, hỗ trợ tìm kiếm theo tên sản phẩm
 * và lọc theo thương hiệu (brand_id), danh mục (category_id) và trạng thái (status).
 * Ngoài ra, hỗ trợ sắp xếp theo giá (original_price) theo thứ tự tăng dần hoặc giảm dần.
 *
 * @param mysqli $conn     Kết nối CSDL.
 * @param int    $page     Trang hiện tại.
 * @param int    $limit    Số sản phẩm trên mỗi trang.
 * @param string $search   Từ khóa tìm kiếm theo tên sản phẩm.
 * @param string $brand    Lọc theo thương hiệu (brand_id) hoặc rỗng nếu không lọc.
 * @param string $category Lọc theo danh mục (category_id) hoặc rỗng nếu không lọc.
 * @param string $status   Lọc theo trạng thái ("1" hoặc "2") hoặc rỗng nếu không lọc.
 * @param string $sortPrice Sắp xếp theo giá: "asc" (tăng dần), "desc" (giảm dần) hoặc rỗng (mặc định sắp xếp theo tên).
 *
 * @return array Trả về mảng gồm:
 *   - products: danh sách sản phẩm (mysqli_result)
 *   - totalPages: tổng số trang
 *   - currentPage: trang hiện tại
 *   - totalProducts: tổng số sản phẩm tìm được
 */
function getProductsWithPagination($conn, $page = 1, $limit = 10, $search = "", $brand = "", $category = "", $status = "", $sortPrice = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);
    
    $conditions = [];
    $bindTypes  = "";
    $params     = [];
    
    $conditions[] = "p.product_name LIKE ?";
    $bindTypes   .= "s";
    $params[]    = "%" . $search . "%";
    
    if ($brand !== "") {
        $conditions[] = "p.brand_id = ?";
        $bindTypes   .= "s";
        $params[]    = $brand;
    }
    if ($category !== "") {
        $conditions[] = "p.category_id = ?";
        $bindTypes   .= "s";
        $params[]    = $category;
    }
    if ($status !== "" && in_array($status, ["1", "2"])) {
        $conditions[] = "p.status = ?";
        $bindTypes   .= "i";
        $params[]    = (int)$status;
    }
    
    $whereClause = implode(" AND ", $conditions);
    
    $sqlCount = "SELECT COUNT(*) as total FROM product p WHERE " . $whereClause;
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param($bindTypes, ...$params);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount = $resultCount->fetch_assoc();
    $totalProducts = (int)($rowCount['total'] ?? 0);
    
    $totalPages = max(1, ceil($totalProducts / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;
    
    if ($sortPrice === "asc" || $sortPrice === "desc") {
        $orderBy = "ORDER BY p.original_price " . strtoupper($sortPrice);
    } else {
        $orderBy = "ORDER BY p.product_name ASC";
    }
    
    $sql = "SELECT p.*, b.brand_name, c.category_name
            FROM product p
            LEFT JOIN brand b ON p.brand_id = b.brand_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE " . $whereClause . " " . $orderBy . "
            LIMIT ? OFFSET ?";
    
    $bindTypesWithLimit = $bindTypes . "ii";
    $paramsWithLimit = array_merge($params, [$limit, $offset]);
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($bindTypesWithLimit, ...$paramsWithLimit);
    $stmt->execute();
    $products = $stmt->get_result();
    
    return [
        'products'      => $products,
        'totalPages'    => $totalPages,
        'currentPage'   => $page,
        'totalProducts' => $totalProducts
    ];
}

/**
 * Lấy thông tin chi tiết của sản phẩm, bao gồm sản phẩm chính và các biến thể của nó.
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm.
 *
 * @return array|null Trả về mảng gồm:
 *   - product: thông tin sản phẩm từ bảng product.
 *   - variants: mảng các biến thể, mỗi biến thể gồm thông tin từ bảng product_variants
 *                và một mảng các ảnh liên quan (lấy từ bảng images qua variant_images).
 * Nếu không tìm thấy sản phẩm thì trả về null.
 */
function getProductDetail($conn, $product_id) {
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    if (!$product) {
        return null;
    }
    
    $sqlVariants = "SELECT * FROM product_variants WHERE product_id = ?";
    $stmtVariants = $conn->prepare($sqlVariants);
    $stmtVariants->bind_param("s", $product_id);
    $stmtVariants->execute();
    $variantsResult = $stmtVariants->get_result();
    
    $variants = [];
    while ($variant = $variantsResult->fetch_assoc()) {
        $sqlImages = "SELECT i.image_id, i.image_url, i.caption
                      FROM images i
                      INNER JOIN variant_images vi ON i.image_id = vi.image_id
                      WHERE vi.variant_id = ?";
        $stmtImages = $conn->prepare($sqlImages);
        $stmtImages->bind_param("s", $variant['variant_id']);
        $stmtImages->execute();
        $imagesResult = $stmtImages->get_result();
        $images = [];
        while ($img = $imagesResult->fetch_assoc()) {
            $images[] = $img;
        }
        $variant['images'] = $images;
        $variants[] = $variant;
    }
    
    return [
        'product'  => $product,
        'variants' => $variants
    ];
}

/**
 * Xoá sản phẩm khỏi DB và xoá file ảnh (nếu có).
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm cần xoá.
 *
 * @return bool True nếu xoá thành công, false nếu thất bại.
 */
function deleteProduct($conn, $product_id) {
    $product = getProductById($conn, $product_id);
    if ($product && !empty($product['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $product['image_url'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }
    
    $sql = "DELETE FROM product WHERE product_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $product_id);
        if (!$stmt->execute()) {
            error_log("Lỗi xoá sản phẩm: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    } else {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
}

/**
 * Xử lý xoá sản phẩm thông qua form.
 * Nếu xoá thành công, chuyển hướng về trang danh sách sản phẩm.
 *
 * @param mysqli $conn       Kết nối CSDL.
 * @param string $product_id ID sản phẩm cần xoá.
 */
function processDeleteProduct($conn, $product_id) {
    if (deleteProduct($conn, $product_id)) {
        header("Location: index.php?msg=Xoá sản phẩm thành công!&type=success");
        exit;
    } else {
        echo "Xoá sản phẩm thất bại. Vui lòng thử lại.";
    }
}
?>