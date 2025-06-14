<?php

require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Dashboard($db);
    }

    public function getStats()
    {
        echo json_encode([
            'status' => 200,
            'data' => [
                'analytics' => $this->model->getAnalytics(),
                'order_overview' => $this->model->getOrderOverview(),
                'sales_by_category' => $this->model->getSalesByCategory(),
                'new_orders' => $this->model->getNewOrders(),
                'top_products' => $this->model->getTopSellingProducts()
            ]
        ]);
    }
}
