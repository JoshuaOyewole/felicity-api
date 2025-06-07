<?php

use Config\MailService;

require_once __DIR__ . '/../models/Order.php';

class OrderRequestController
{
    private $order_request;

    public function __construct($db)
    {
        $this->order_request = new OrderRequest($db);
    }


    public function getAll()
    {
        header('Content-Type: application/json');
        $data = $this->order_request->getAllOrderRequest();

        if (empty($data)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No order request found',
                'data' => []
            ]);
            return;
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Order requests fetched successfully',
            'data' => $data
        ]);
    }
    // Method to delete an order request by ID
    public function delete($id)
    {
        header('Content-Type: application/json');
        $result = $this->order_request->deleteOrderRquest($id);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } else if ($result) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Order request deleted successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No order request found with that ID'
            ]);
        }
    }
    public function getOne($id)
    {
        header('Content-Type: application/json');

        $quote = $this->order_request->getOrderRequestById($id);

        if (!$quote) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Order request not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Order request fetched successfully',
            'data' => $quote
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');

        // Get POST data
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fullnames']) || !isset($data['product_name']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Fullnames, Email, Product Name, Qty, and  Phone No are required'
            ]);
            return;
        }

        $result = $this->order_request->createOrderRequest($data);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            // Send email notification
            $mailService = new MailService();
            $emailSent = $mailService->sendOrderRequestNotification($data);

            if (!$emailSent) {
                error_log("Failed to send email notification for order with ID $result");
            }
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Order request created successfully',
                'id' => $result,
                'email_sent' => $emailSent
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'An error occurred while creating the request'
            ]);
        }
    }


}
?>