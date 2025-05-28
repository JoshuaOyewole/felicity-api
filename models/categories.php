<?php
class Categories
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function categoryExists($id)
    {
        $sql = "SELECT id FROM categories  WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
    // Fetch all categories
    public function getAllCategories()
    {
        try {
            $sql = "SELECT * FROM categories ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $categories;
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            return ['error' => 'Failed to fetch categories: ' . $e->getMessage()];
        }
    }
    public function deleteCategory($id)
    {
        try {
            // Prepare the delete query
            $sql = "DELETE FROM categories WHERE id = :id";
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
    public function getCategoryById($id)
    {
        try {
            $sql = "SELECT 
                    *
                FROM categories 
                LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            return $category ?: null;
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch category: ' . $e->getMessage()];
        }
    }
    public function createCategory($data)
    {
        try {
            $sql = "INSERT INTO categories (category_name, slug, created_at) VALUES (:category_name, :slug, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (
                !isset($data['category_name']) ||
                !isset($data['slug']) 
            ) {
                return ['error' => 'missing required fields (category name and slug)'];
            }

            $stmt->bindParam(':category_name', $data['category_name']);
            $stmt->bindParam(':slug', $data['slug']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new testimonial
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create Category: ' . $e->getMessage()];
        }
    }
    public function updateCategory($id, $category_name, $slug)
    {
        $result = $this->categoryExists($id);



        if (!$result) {
            return ['error' => 'Category with the given ID does not exist'];
        }
        try {
            $sql = "UPDATE categories SET title = :title, content = :content, category = :category WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':category_name', $category_name);
            $stmt->bindParam(':slug', $slug);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to update category: ' . $e->getMessage()];
        }
    }
}
