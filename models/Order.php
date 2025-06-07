<?php
class OrderRequest
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fetch all quotes
    public function getAllOrderRequest()
    {
        try {
            $sql = "SELECT * FROM order_requests ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch order requests: ' . $e->getMessage()];
        }
    }
    public function getOrderRequestById($id)
    {
        try {
            $sql = "SELECT * FROM order_requests WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch order requests: ' . $e->getMessage()];
        }
    }

    public function deleteOrderRquest($id)
    {
        try {
            $sql = "DELETE FROM order_requests WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Successfully deleted
            } else {
                return false; // No row found with this ID
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to order request: ' . $e->getMessage()];
        }
    }
    public function createOrderRequest($data)
    {
        try {
            $sql = "INSERT INTO order_requests (product_name, email, phone,fullnames, additionalMessage,qty, created_at) VALUES (:product_name, :email, :phone, :fullnames, :additionalMessage, :qty, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (!isset($data['product_name']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['qty']) || !isset($data['fullnames'])) {
                return ['error' => 'Fullnames, email and product name and phone are required'];
            }

            $stmt->bindParam(':product_name', $data['product_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':fullnames', $data['fullnames']);
            $stmt->bindParam(':additionalMessage', $data['additionalMessage']);
            $stmt->bindParam(':qty', $data['qty']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new request
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create order request: ' . $e->getMessage()];
        }
    }

}

