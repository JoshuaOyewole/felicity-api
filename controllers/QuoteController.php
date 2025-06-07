<?php

use Config\MailService;

require_once __DIR__ . '/../models/Quote.php';

class QuoteController
{
    private $quote;

    public function __construct($db)
    {
        $this->quote = new Quote($db);
    }


    public function getAll()
    {
        header('Content-Type: application/json');
        $data = $this->quote->getAllquotes();

        if (empty($data)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No quote found',
                'data' => []
            ]);
            return;
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Quotes fetched successfully',
            'data' => $data
        ]);
    }
    // Method to delete a quote by ID
    public function delete($id)
    {
        header('Content-Type: application/json');
        $result = $this->quote->deleteQuote($id);

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
                'message' => 'Quote deleted successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No quote found with that ID'
            ]);
        }
    }
    public function getOne($id)
    {
        header('Content-Type: application/json');

        $quote = $this->quote->getQuoteById($id);

        if (!$quote) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Quote not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Quote fetched successfully',
            'data' => $quote
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');

        // Get POST data
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fullnames']) || !isset($data['budget_range']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Fullnames, email, and budget_range are required'
            ]);
            return;
        }

        $result = $this->quote->createQuote($data);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            // Send email notification
            $mailService = new MailService();
            $emailSent = $mailService->sendQuoteNotification($data);

            if (!$emailSent) {
                error_log("Failed to send email notification for quote ID $result");
            }
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Quote created successfully',
                'id' => $result,
                'email_sent' => $emailSent
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'An error occurred while creating the quote'
            ]);
        }
    }


}
?>