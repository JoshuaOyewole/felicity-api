<?php
class Product
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function productExists($id)
    {
        $sql = "SELECT id FROM products WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
    // Fetch all products
    public function getAllProducts()
    {
        try {
            // $sql = "SELECT * FROM products ORDER BY created_at DESC";
            $sql = "SELECT 
    p.id,
    p.product_name,
    p.price,
    p.rating,
    p.total_stock,
    p.discount_rate,
    c.category_name,
    p.image_1,
    p.image_2,
    p.image_3,
    p.image_4,
    p.image_5,
    p.key_features,
    p.description,
    p.created_at
FROM products p
JOIN categories c ON p.category_id = c.id
ORDER BY p.created_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $products;
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            return ['error' => 'Failed to fetch products: ' . $e->getMessage()];
        }
    }
    public function getProductsByCategory($categoryId)
    {
        $query = "SELECT * FROM products WHERE category_id = :category_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    }


    public function deleteProduct($id)
    {
        try {
            // Prepare the delete query
            $sql = "DELETE FROM products WHERE id = :id";
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
    public function getProductById($id)
    {
        try {
            $sql = "SELECT 
                    p.*, 
                    c.category_name 
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id
                LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ?: null;
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch product: ' . $e->getMessage()];
        }
    }

    public function createProduct($data)
    {
        try {
            $sql = "INSERT INTO products (product_name, 
            category_id, 
            price, 
            discount_rate, 
            total_stock, 
            image_1, 
            image_2, 
            image_3, 
            image_4, 
            image_5, 
            key_features, 
            description, 
            created_at
            ) VALUES 
            ( 
            :product_name, 
            :category_id, 
            :price, 
            :discount_rate, 
            :total_stock, 
            :image_1, 
            :image_2, 
            :image_3, 
            :image_4, 
            :image_5, 
            :key_features, 
            :description, 
            NOW())";
            $stmt = $this->conn->prepare($sql);

            if (
                !isset($data['product_name']) ||
                !isset($data['category_id']) ||
                !isset($data['price']) ||
                !isset($data['image_1']) ||
                !isset($data['image_2']) ||
                !isset($data['description'])
            ) {
                return ['error' => 'some fields are required.'];
            }

            $stmt->bindParam(':product_name', $data['product_name']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':discount_rate', $data['discount_rate']);
            $stmt->bindParam(':total_stock', $data['total_stock']);
            $stmt->bindParam(':image_1', $data['image_1']);
            $stmt->bindParam(':image_2', $data['image_2']);
            $stmt->bindParam(':image_3', $data['image_3']);
            $stmt->bindParam(':image_4', $data['image_4']);
            $stmt->bindParam(':image_5', $data['image_5']);
            $stmt->bindParam(':key_features', $data['key_features']);
            $stmt->bindParam(':description', $data['description']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new testimonial
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create Product: ' . $e->getMessage()];
        }
    }
    /*    public function updateProduct($id, $product_name, $category_id, $discount_rate, $price, $description, $key_features, $image_1, $image_2, $image_3, $image_4, $image_5)
       {
           if (!$this->ProductExists($id)) {
               return ['error' => 'Product with the given ID does not exist'];
           }

           try {
               $sql = "UPDATE products SET 
                       product_name = :product_name, 
                       category_id = :category_id, 
                       price = :price, 
                       description = :description, 
                       discount_rate = :discount_rate, 
                       key_features = :key_features, 
                       image_1 = :image_1, 
                       image_2 = :image_2, 
                       image_3 = :image_3, 
                       image_4 = :image_4, 
                       image_5 = :image_5 
                   WHERE id = :id";

               $stmt = $this->conn->prepare($sql);

               $stmt->bindValue(':product_name', $product_name);
               $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
               $stmt->bindValue(':price', $price);
               $stmt->bindValue(':description', $description);
               $stmt->bindValue(':discount_rate', $discount_rate);
               $stmt->bindValue(':key_features', $key_features);
               $stmt->bindValue(':image_1', $image_1);
               $stmt->bindValue(':image_2', $image_2);
               $stmt->bindValue(':image_3', $image_3);
               $stmt->bindValue(':image_4', $image_4);
               $stmt->bindValue(':image_5', $image_5);
               $stmt->bindValue(':id', $id, PDO::PARAM_INT);

               if ($stmt->execute()) {
                   return true;
               } else {
                   return false;
               }
           } catch (PDOException $e) {
               return ['error' => 'Failed to update product: ' . $e->getMessage()];
           }
       } */
    public function updateProductPartial($id, $fields)
    {
        if (!$this->ProductExists($id)) {
            return ['error' => 'Product with the given ID does not exist'];
        }

        try {
            $setParts = [];
            $params = [];

            foreach ($fields as $column => $value) {
                $setParts[] = "$column = :$column";
                $params[":$column"] = $value;
            }

            $params[':id'] = $id;
            $setClause = implode(', ', $setParts);

            $sql = "UPDATE products SET $setClause WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return ['error' => 'Failed to update product: ' . $e->getMessage()];
        }
    }

}
