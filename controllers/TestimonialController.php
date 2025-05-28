<?php
require_once __DIR__ . '/../models/Testimonials.php';

class TestimonialController
{
    private $testimonial;

    public function __construct($db)
    {
        $this->testimonial = new Testimonial($db);
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        $data = $this->testimonial->getAllTestimonials();

        if (empty($data)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No testimonial found',
                'data' => []
            ]);
            return;
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Testimonials fetched successfully',
            'data' => $data
        ]);
    }
    // Method to delete a testimonial by ID
    public function delete($id)
    {
        header('Content-Type: application/json');
        $result = $this->testimonial->deleteTestimonial($id);

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
                'message' => 'Testimonial deleted successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No testimonial found with that ID'
            ]);
        }
    }
    public function create()
    {
        header('Content-Type: application/json');

        // Get POST data
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name']) || !isset($data['location']) || !isset($data['content'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Name , location and content are required'
            ]);
            return;
        }

        $result = $this->testimonial->createTestimonial($data);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Testimonial created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'An error occurred while creating the testimonial'
            ]);
        }
    }

}
?>