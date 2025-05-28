<?php
require_once __DIR__ . '/../models/categories.php';

class CategoriesController
{
    private $category;

    public function __construct($db)
    {
        $this->category = new Categories($db);
    }
 public function getOne($id)
    {
        header('Content-Type: application/json');

        $category = $this->category->getCategoryById($id);

        if (!$category) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Category not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Category fetched successfully',
            'data' => $category
        ]);
    }
    public function getAll()
    {
        header('Content-Type: application/json');
        $allCategories = $this->category->getAllCategories();

        if (empty($allCategories)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No category found',
                'data' => []
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Categories fetched successfully',
            'data' => $allCategories
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        $result = $this->category->createCategory($input);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            echo json_encode([
                'status' => 201,
                'message' => 'Category created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to create category'
            ]);
        }
    }
    public function delete($id)
    {
        header('Content-Type: application/json');

        // Attempt to delete the category post with the provided ID
        $result = $this->category->deleteCategory($id);

        // If deleteCategory returns an error (such as database issues or invalid ID)
        if (is_array($result) && isset($result['error'])) {
            // Log the error to ensure we know what went wrong in the backend (optional)
            error_log('Failed to delete category: ' . $result['error']);

            // Respond with a 500 status for server-side errors
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        }
        // If deletion is successful
        else if ($result) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Category deleted successfully'
            ]);
        }
        // If no record was found with the provided ID (or failed deletion)
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No category found with that ID'
            ]);
        }
    }
    // In the controller

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['category_name ']) || !isset($data['slug'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Missing required fields: category_name, slug'
            ]);
            return;
        }

        $result = $this->category->updateCategory($id, $data['category_name'], $data['slug']);

        if (isset($result['error'])) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'error' => $result['error']
            ]);
            return;
        }

        if ($result) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Category updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to update the Category'
            ]);
        }
    }


}
