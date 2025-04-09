<?php
// File: admin/controller/productController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4.
 *
 * @return string UUID v4.
 */
function generateUCCID() {
    $data = random_bytes(16);
    // Thiết lập phiên bản 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Thiết lập biến thể
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf('%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

/**
 * Kiểm tra xem tên sản phẩm đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa).
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

function getProductsWithPagination($conn, $page, $limit, $search, $brand, $category, $status, $sortPrice) {
    $offset = ($page - 1) * $limit;

    $sql = "SELECT p.*, b.brand_name, c.category_name
            FROM product p
            LEFT JOIN brand b ON p.brand_id = b.brand_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE 1";

    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND p.product_name LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
    }

    if (!empty($brand)) {
        $sql .= " AND p.brand_id = ?";
        $params[] = $brand;
        $types .= "s";
    }

    if (!empty($category)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category;
        $types .= "s";
    }

    if ($status !== "") {
        $sql .= " AND p.status = ?";
        $params[] = $status;
        $types .= "s";
    }

    // Sắp xếp theo giá
    if ($sortPrice === "asc") {
        $sql .= " ORDER BY p.discount_price ASC";
    } elseif ($sortPrice === "desc") {
        $sql .= " ORDER BY p.discount_price DESC";
    } else {
        $sql .= " ORDER BY p.product_id DESC"; // mặc định
    }

    // Thêm LIMIT và OFFSET
    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";

    // Chuẩn bị statement
    $stmt = $conn->prepare($sql);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Đếm tổng số sản phẩm
    $countSql = "SELECT COUNT(*) as total
                 FROM product p
                 WHERE 1";
    $countParams = [];
    $countTypes = "";

    if (!empty($search)) {
        $countSql .= " AND p.product_name LIKE ?";
        $countParams[] = "%$search%";
        $countTypes .= "s";
    }

    if (!empty($brand)) {
        $countSql .= " AND p.brand_id = ?";
        $countParams[] = $brand;
        $countTypes .= "s";
    }

    if (!empty($category)) {
        $countSql .= " AND p.category_id = ?";
        $countParams[] = $category;
        $countTypes .= "s";
    }

    if ($status !== "") {
        $countSql .= " AND p.status = ?";
        $countParams[] = $status;
        $countTypes .= "s";
    }

    $stmtCount = $conn->prepare($countSql);
    if (!empty($countTypes)) {
        $stmtCount->bind_param($countTypes, ...$countParams);
    }
    $stmtCount->execute();
    $countResult = $stmtCount->get_result();
    $totalProducts = $countResult->fetch_assoc()['total'];

    $totalPages = ceil($totalProducts / $limit);

    return [
        "products"      => $products,
        "totalProducts" => $totalProducts,
        "totalPages"    => $totalPages,
        "currentPage"   => $page
    ];
}



/**
 * Thêm sản phẩm mới (bao gồm upload ảnh chính).
 * Ảnh chính được lưu với tên: {slugName}-main-{timestamp}.ext
 */
function addProductWithMainImage($conn, $productName, $description, $price, $categoryId, $brandId, $status, $mainImage) {
    $product_id = generateUCCID();
    
    $sql = "INSERT INTO product 
            (product_id, product_name, description, price, category_id, brand_id, status, main_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdsiis", 
        $product_id, 
        $productName, 
        $description, 
        $price, 
        $categoryId, 
        $brandId, 
        $status, 
        $mainImage
    );
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result ? $product_id : false;
}

/**
 * Xử lý thêm sản phẩm thông qua form.
 * Quy trình:
 *  1. Kiểm tra dữ liệu (ràng buộc không được để trống,…).
 *  2. Upload ảnh chính.
 *  3. Thêm sản phẩm (bảng product).
 *
 * @return array|null Mã sản phẩm nếu thành công, mảng lỗi nếu có lỗi.
 */
function processAddProduct($conn) {
    $errors = [];
    $productId = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu từ form
        $productName = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['content'] ?? '');  // Sử dụng field từ Quill Editor
        $price       = $_POST['original_price'] ?? 0;
        $categoryId  = trim($_POST['category_id'] ?? '');
        $brandId     = trim($_POST['brand_id'] ?? '');
        $status      = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        // Validate dữ liệu
        if (empty($productName)) {
            $errors['product_name'] = "Tên sản phẩm không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $productName)) {
            $errors['product_name'] = "Tên sản phẩm không được chứa ký tự đặc biệt.";
        }
        if (empty($description)) {
            $errors['content'] = "Mô tả sản phẩm không được để trống.";
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors['original_price'] = "Giá sản phẩm không hợp lệ.";
        }
        if (empty($categoryId)) {
            $errors['category_id'] = "Vui lòng chọn danh mục.";
        }
        if (empty($brandId)) {
            $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
        }
        if (empty($_FILES['main_image']['name'])) {
            $errors['main_image'] = "Vui lòng chọn ảnh chính cho sản phẩm.";
        }
        
        // Nếu không có lỗi, xử lý upload ảnh chính
        $mainImage = null;
        if (empty($errors)) {
            $targetDir = __DIR__ . '/../uploads/products/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            $slugName  = safeString($productName);
            $timestamp = date('YmdHis');
            $filename  = "{$slugName}-main-{$timestamp}." . $extension;
            $targetFile = $targetDir . $filename;
            
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $targetFile)) {
                $mainImage = 'admin/uploads/products/' . $filename;
            } else {
                $errors['main_image'] = "Tải ảnh chính lên thất bại.";
            }
        }
        
        // Thêm sản phẩm nếu không có lỗi
        if (empty($errors)) {
            $productId = addProductWithMainImage($conn, $productName, $description, $price, $categoryId, $brandId, $status, $mainImage);
            if (!$productId) {
                $errors['general'] = "Thêm sản phẩm thất bại.";
            }
        }
    }
    
    return empty($errors) ? $productId : $errors;
}

/**
 * Xử lý thêm biến thể.
 * Bạn có thể triển khai logic lưu vào bảng product_variants theo dạng:
 * - Với mỗi biến thể: color_id, size_id, quantity.
 * - Lưu ánh xạ color_id => variant_id để phục vụ upload ảnh sau.
 *
 * @param mysqli $conn
 * @param string $productId
 * @return array|null Mã ánh xạ biến thể hoặc mảng lỗi.
 */
function processAddVariant($conn, $productId) {
    $errors = [];
    $colorVariantMap = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            foreach ($_POST['variants'] as $variant) {
                $color_id = trim($variant['color_id'] ?? '');
                $size_id  = trim($variant['size_id'] ?? '');
                $quantity = (int)($variant['quantity'] ?? 0);
                
                if ($color_id && $quantity > 0) {
                    $variant_id = generateUCCID();
                    $sql = "INSERT INTO product_variants 
                            (variant_id, product_id, color_id, size_id, quantity, status)
                            VALUES (?, ?, ?, ?, ?, 1)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $variant_id, $productId, $color_id, $size_id, $quantity);
                    if (!$stmt->execute()) {
                        $errors['variants'] = "Thêm biến thể thất bại cho color_id = $color_id";
                        error_log("Lỗi thêm biến thể: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    // Lưu ánh xạ để upload ảnh cho biến thể này
                    $colorVariantMap[$color_id] = $variant_id;
                }
            }
        } else {
            $errors['variants'] = "Vui lòng thêm ít nhất một biến thể.";
        }
    }
    
    return empty($errors) ? $colorVariantMap : $errors;
}

/**
 * Xử lý upload ảnh cho biến thể.
 * Mỗi file ảnh sẽ được đặt tên theo quy ước: {slugProduct}-{slugColor}-{stt}.ext
 *
 * @param mysqli $conn
 * @param string $productId
 * @return array Mảng lỗi nếu có.
 */
function processAddVariantImages($conn, $productId) {
    $errors = [];
    
    // Giả định rằng file upload chứa: variant_images[] và variant_color[]
    if (empty($_FILES['variant_images']['name'][0]) || !isset($_POST['variant_color'])) {
        $errors['variant_images'] = "Vui lòng chọn ít nhất một ảnh cho biến thể.";
        return $errors;
    }
    
    // Nếu đã có productId, bạn có thể lấy danh sách biến thể từ bảng product_variants theo productId
    // Ở đây, giả sử $colorVariantMap đã được lưu trong session (hoặc bạn tái tạo từ DB)
    // Ví dụ: session_start(); $colorVariantMap = $_SESSION['colorVariantMap'];
    // Đối với ví dụ này, mình giả sử rằng biến này được khôi phục được.
    // Ở thực tế bạn nên tái xây dựng ánh xạ dựa trên dữ liệu của product_variants
    $colorVariantMap = []; 
    // Giả sử bạn có một hàm lấy ánh xạ: getVariantMapping($conn, $productId);
    // $colorVariantMap = getVariantMapping($conn, $productId);
    
    // Nếu không có ánh xạ, bạn có thể duyệt theo variant_color của form và tự sử dụng nó.
    $slugProduct = safeString("product" . $productId);  // Tùy chọn: dùng tên sản phẩm nếu có
    $imageIndex  = 1;
    
    foreach ($_FILES['variant_images']['name'] as $i => $fileName) {
        $color_id = trim($_POST['variant_color'][$i] ?? '');
        // Nếu không có ánh xạ từ DB, giả sử variant_id chính là color_id (hoặc bạn tự xác định)
        $variant_id = $colorVariantMap[$color_id] ?? $color_id;
        if ($variant_id) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $colorName = isset($_POST['variant_color_name'][$i]) ? $_POST['variant_color_name'][$i] : $color_id;
            $slugColor = safeString($colorName);
            $newFileName = "{$slugProduct}-{$slugColor}-{$imageIndex}." . $extension;
            
            $targetDir = __DIR__ . '/../uploads/product_variants/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $targetFile  = $targetDir . $newFileName;
            
            if (move_uploaded_file($_FILES['variant_images']['tmp_name'][$i], $targetFile)) {
                $imageUrl = 'admin/uploads/product_variants/' . $newFileName;
                
                $variantImageId = generateUCCID();
                $sql = "INSERT INTO variant_images 
                        (variant_image_id, variant_id, image_url, sort_order, status)
                        VALUES (?, ?, ?, 0, 1)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $variantImageId, $variant_id, $imageUrl);
                if (!$stmt->execute()) {
                    error_log("Lỗi thêm ảnh biến thể: " . $stmt->error);
                    $errors['variant_images'] = "Tải ảnh biến thể thất bại cho variant_id = $variant_id";
                }
                $stmt->close();
            } else {
                $errors['variant_images'] = "Tải ảnh biến thể thất bại (move_uploaded_file).";
            }
            $imageIndex++;
        }
    }
    
    return $errors;
}

/**
 * (Tùy chọn) Hàm lấy thông tin sản phẩm theo product_id.
 */
function getProductById($conn, $productId) {
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}

/**
 * (Tùy chọn) Hàm lấy danh sách biến thể theo product_id.
 */
function getProductVariants($conn, $productId) {
    $sql = "SELECT * FROM product_variants WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $variants = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $variants;
}
function getAllCategories($conn) {
    $sql = "SELECT * FROM category WHERE status = 1 ORDER BY category_name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
function getAllBrands($conn) {
    $sql = "SELECT * FROM brand WHERE status = 1 ORDER BY brand_name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>