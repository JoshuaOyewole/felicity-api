<?php
class Testimonial
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Fetch all testimonials
    public function getAllTestimonials()
    {
        try {
            $sql = "SELECT * FROM testimonials ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch testimonials: ' . $e->getMessage()];
        }
    }

    public function deleteTestimonial($id)
    {
        try {
            $sql = "DELETE FROM testimonials WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Successfully deleted
            } else {
                return false; // No row found with this ID
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to delete testimonial: ' . $e->getMessage()];
        }
    }
    public function createTestimonial($data)
    {
        try {
            $sql = "INSERT INTO testimonials (name, content,location, created_at) VALUES (:name, :content, :location, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (!isset($data['name']) || !isset($data['content']) || !isset($data['location'])) {
                return ['error' => 'Name and content are required'];
            }

            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':location', $data['location']);
            $stmt->bindParam(':content', $data['content']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new testimonial
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create testimonial: ' . $e->getMessage()];
        }
    }

}

