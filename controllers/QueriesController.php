<?php

use Config\MailService;

require_once __DIR__ . '/../models/Queries.php';

class QueriesController
{
    private $queries;

    public function __construct($db)
    {
        $this->queries = new Queries($db);
    }


    public function getAll()
    {
        header('Content-Type: application/json');
        $data = $this->queries->getAllQueries();

        if (empty($data)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No query request found',
                'data' => []
            ]);
            return;
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Query fetched successfully',
            'data' => $data
        ]);
    }
    // Method to delete an query by ID
    public function delete($id)
    {
        header('Content-Type: application/json');
        $result = $this->queries->deleteQuery($id);

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
                'message' => 'Query deleted successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No query found with that ID'
            ]);
        }
    }
    public function getOne($id)
    {
        header('Content-Type: application/json');

        $query = $this->queries->getQueryById($id);

        if (!$query) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'query not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Query fetched successfully',
            'data' => $query
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');

        // Get POST data
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['firstname']) || !isset($data['message']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Firstname, Email, and Message are required'
            ]);
            return;
        }

        $result = $this->queries->createQuery($data);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            // Send email notification
            $mailService = new MailService();
            $emailSent = $mailService->sendQueryNotification($data);

            if (!$emailSent) {
                error_log("Failed to send email notification for query with ID $result");
            }
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Query sent successfully',
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