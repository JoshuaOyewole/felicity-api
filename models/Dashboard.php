<?php

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAnalytics() {
        $data = [];

        $data['total_orders'] = $this->conn->query("SELECT COUNT(*) FROM order_requests")->fetchColumn();
        $data['total_products'] = $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $data['total_blogs'] = $this->conn->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $data['total_installers'] = $this->conn->query("SELECT COUNT(*) FROM installers")->fetchColumn();

        return $data;
    }

 public function getOrderOverview() {
    $query = "
        SELECT m.month, COALESCE(o.Orders, 0) as Orders
        FROM (
            SELECT 'Jan' AS month, 1 AS month_num UNION
            SELECT 'Feb', 2 UNION
            SELECT 'Mar', 3 UNION
            SELECT 'Apr', 4 UNION
            SELECT 'May', 5 UNION
            SELECT 'Jun', 6 UNION
            SELECT 'Jul', 7 UNION
            SELECT 'Aug', 8 UNION
            SELECT 'Sep', 9 UNION
            SELECT 'Oct', 10 UNION
            SELECT 'Nov', 11 UNION
            SELECT 'Dec', 12
        ) AS m
        LEFT JOIN (
            SELECT MONTH(created_at) as month_num, COUNT(*) as Orders
            FROM order_requests
            GROUP BY MONTH(created_at)
        ) AS o ON m.month_num = o.month_num
        ORDER BY m.month_num
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getSalesByCategory() {
        $query = "
            SELECT c.category_name AS category, COUNT(o.id) AS orders
            FROM order_requests o
            JOIN products p ON o.id = p.id
            JOIN categories c ON p.category_id = c.id
            GROUP BY c.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewOrders($limit = 10) {
        $query = "SELECT * FROM order_requests ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($limit = 5) {
        $query = "
            SELECT p.product_name, COUNT(o.id) as total_orders
            FROM order_requests o
            JOIN products p ON o.id = p.id
            GROUP BY o.id
            ORDER BY total_orders DESC
            LIMIT :limit
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
