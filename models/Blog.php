<?php
class Blog
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function blogExists($id)
    {
        $sql = "SELECT id FROM articles WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
    // Fetch all blogs
    public function getAllBlogs($limit, $offset)
    {
        try {

            $countSql = "SELECT COUNT(*) as total FROM articles";
            $countStmt = $this->conn->prepare($countSql);
            $countStmt->execute();
            $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();


            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $blogs,
                'total' => $total
            ];
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            return [
                'data' => [],
                'total' => 0,
                'error' => 'Failed to fetch products: ' . $e->getMessage()
            ];
        }
    }
    public function deleteBlog($id)
    {
        try {
            // Prepare the delete query
            $sql = "DELETE FROM articles WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            // Bind the ID parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the query and check if any rows were affected
            if ($stmt->execute()) {
                // If no rows are affected, return false (i.e., no record with the ID was found)
                if ($stmt->rowCount() > 0) {
                    return true; // Successfully deleted
                } else {
                    return false; // No record with that ID was found
                }
            } else {
                // Return error if query execution fails
                return ['error' => 'Failed to execute the delete query'];
            }
        } catch (PDOException $e) {
            // Catch any PDOException and return the error message
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
    public function getBlogById($id)
    {
        try {
            $sql = "SELECT 
                    *
                FROM articles 
                WHERE id =:id
                LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $article = $stmt->fetch(PDO::FETCH_ASSOC);
            return $article ?: null;
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch article: ' . $e->getMessage()];
        }
    }
    public function createBlog($data)
    {
        try {
            $sql = "INSERT INTO articles (title, thumbnail,content,category, created_at) VALUES (:title, :thumbnail,:content,:category, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (
                !isset($data['title']) ||
                !isset($data['thumbnail']) ||
                !isset($data['content']) ||
                !isset($data['category'])
            ) {
                return ['error' => 'All fields are required.'];
            }

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':thumbnail', $data['thumbnail']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':category', $data['category']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new testimonial
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create Blog: ' . $e->getMessage()];
        }
    }
    public function updateBlog($id, $title, $content, $category)
    {
        $result = $this->blogExists(($id));



        if (!$result) {
            return ['error' => 'Blog with the given ID does not exist'];
        }
        try {
            $sql = "UPDATE articles SET title = :title, content = :content, category = :category WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':category', $category);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to update blog: ' . $e->getMessage()];
        }
    }
}
