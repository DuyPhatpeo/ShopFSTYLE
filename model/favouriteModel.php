<?php
class FavouriteModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function isFavourite($customer_id, $product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM favourite WHERE customer_id = ? AND product_id = ?");
        $stmt->bind_param("ss", $customer_id, $product_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function addFavourite($customer_id, $product_id) {
        $uuid = uniqid('fav_');
        $stmt = $this->conn->prepare("INSERT INTO favourite (favourite_id, customer_id, product_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uuid, $customer_id, $product_id);
        return $stmt->execute();
    }

    public function removeFavourite($customer_id, $product_id) {
        $stmt = $this->conn->prepare("DELETE FROM favourite WHERE customer_id = ? AND product_id = ?");
        $stmt->bind_param("ss", $customer_id, $product_id);
        return $stmt->execute();
    }
}
?>