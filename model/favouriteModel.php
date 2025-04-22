<?php
class FavouriteModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function isFavourite($customer_id, $product_id) {
        $sql = "SELECT COUNT(*) as count FROM favourite WHERE customer_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $customer_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    public function addFavourite($customer_id, $product_id) {
        $favourite_id = uniqid();
        $sql = "INSERT INTO favourite (favourite_id, customer_id, product_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $favourite_id, $customer_id, $product_id);
        return $stmt->execute();
    }

    public function removeFavourite($customer_id, $product_id) {
        $sql = "DELETE FROM favourite WHERE customer_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $customer_id, $product_id);
        return $stmt->execute();
    }

    public function getFavourites($customer_id) {
        $sql = "SELECT p.* FROM product p 
                JOIN favourite f ON p.product_id = f.product_id 
                WHERE f.customer_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>