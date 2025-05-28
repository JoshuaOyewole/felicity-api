<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private $product;

    public function __construct($db)
    {
        $this->product = new Product($db);
    }

    public function getByCategory($categoryId)
{
    header('Content-Type: application/json');

    if (!is_numeric($categoryId)) {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'error' => 'Invalid category ID'
        ]);
        return;
    }

    $products = $this->product->getProductsByCategory($categoryId);

    if (empty($products)) {
        http_response_code(404);
        echo json_encode([
            'status' => 404,
            'message' => 'No products found for this category',
            'data' => []
        ]);
        return;
    }

    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'message' => 'Products fetched successfully',
        'data' => $products
    ]);
}


    public function getOne($id)
    {
        header('Content-Type: application/json');

        $product = $this->product->getProductById($id);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Product not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Product fetched successfully',
            'data' => $product
        ]);
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        $allProducts = $this->product->getAllProducts();

        if (empty($allProducts)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No product found',
                'data' => []
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Product fetched successfully',
            'data' => $allProducts
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        $result = $this->product->createProduct($input);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            echo json_encode([
                'status' => 201,
                'message' => 'Product created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to create product'
            ]);
        }
    }
    public function delete($id)
    {
        header('Content-Type: application/json');

        // Attempt to delete the product with the provided ID
        $result = $this->product->deleteProduct($id);

        // If deleteProduct returns an error (such as database issues or invalid ID)
        if (is_array($result) && isset($result['error'])) {
            // Log the error to ensure we know what went wrong in the backend (optional)
            error_log('Failed to delete product: ' . $result['error']);

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
                'message' => 'Product deleted successfully'
            ]);
        }
        // If no record was found with the provided ID (or failed deletion)
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No product found with that ID'
            ]);
        }
    }
    // In the controller
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Invalid product ID'
            ]);
            return;
        }

        // Pass only valid fields for update
        $allowedFields = [
            'product_name',
            'category_id',
            'price',
            'description',
            'key_features',
            'image_1',
            'image_2',
            'image_3',
            'image_4',
            'image_5',
            'discount_rate'
        ];

        $fieldsToUpdate = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fieldsToUpdate[$field] = $data[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'No valid fields provided for update'
            ]);
            return;
        }

        // Call the model method with only fields that should be updated
        $result = $this->product->updateProductPartial($id, $fieldsToUpdate);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Product updated successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'error' => 'Product not found or not updated'
            ]);
        }
    }
}
