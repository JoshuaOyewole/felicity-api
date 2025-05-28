<?php
require_once __DIR__ . '/../models/Blog.php';

class BlogController
{
    private $blog;

    public function __construct($db)
    {
        $this->blog = new Blog($db);
    }
 public function getOne($id)
    {
        header('Content-Type: application/json');

        $article = $this->blog->getBlogById($id);

        if (!$article) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Article not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Article fetched successfully',
            'data' => $article
        ]);
    }
    public function getAll()
    {
        header('Content-Type: application/json');
        $allArticles = $this->blog->getAllBlogs();

        if (empty($allArticles)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No article found',
                'data' => []
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Articles fetched successfully',
            'data' => $allArticles
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        $result = $this->blog->createBlog($input);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            echo json_encode([
                'status' => 201,
                'message' => 'Blog created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to create blog'
            ]);
        }
    }
    public function delete($id)
    {
        header('Content-Type: application/json');

        // Attempt to delete the blog post with the provided ID
        $result = $this->blog->deleteBlog($id);

        // If deleteBlog returns an error (such as database issues or invalid ID)
        if (is_array($result) && isset($result['error'])) {
            // Log the error to ensure we know what went wrong in the backend (optional)
            error_log('Failed to delete blog: ' . $result['error']);

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
                'message' => 'Article deleted successfully'
            ]);
        }
        // If no record was found with the provided ID (or failed deletion)
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No article found with that ID'
            ]);
        }
    }
    // In the controller

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['title']) || !isset($data['content']) || !isset($data['category'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Missing required fields: title, content, category'
            ]);
            return;
        }

        $result = $this->blog->updateBlog($id, $data['title'], $data['content'], $data['category']);

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
                'message' => 'Blog updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to update the blog'
            ]);
        }
    }


}
