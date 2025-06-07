<?php
class Installer
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function installerExists($id)
    {
        $sql = "SELECT id FROM installers WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
    // Fetch all installers
    public function getAllInstallers()
    {
        try {
            $sql = "SELECT 
    i.id,
    i.fullnames,
    i.email,
    i.phone,
    i.address,
    s.state_name,
    i.created_at
FROM installers i
JOIN states s ON i.state_id = s.id
ORDER BY i.created_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $installers;
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            return ['error' => 'Failed to fetch installers: ' . $e->getMessage()];
        }
    }
    public function getInstallerByState($stateId)
    {
        $query = "SELECT * FROM installers WHERE state_id = :state_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':state_id', $stateId, PDO::PARAM_INT);
        $stmt->execute();

        $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $installers;
    }

    public function deleteInstaller($id)
    {
        try {
            // Prepare the delete query
            $sql = "DELETE FROM installers WHERE id = :id";
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
    public function getInstallerById($id)
    {
        try {
            $sql = "SELECT 
                    i.*, 
                    s.state_name 
                FROM installers i
                JOIN states i ON i.state_id = s.id
                WHERE i.id = :id
                LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $installer = $stmt->fetch(PDO::FETCH_ASSOC);
            return $installer ?: null;
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch installer: ' . $e->getMessage()];
        }
    }

    public function createInstaller($data)
    {
        try {
            $sql = "INSERT INTO installers (
            fullnames, 
            state_id, 
            phone, 
            address, 
            email,
            created_at
            ) VALUES 
            ( 
            :fullnames, 
            :state_id, 
            :phone, 
            :address, 
            :email, 
            NOW())";
            $stmt = $this->conn->prepare($sql);

            if (
                !isset($data['fullnames']) ||
                !isset($data['state_id']) ||
                !isset($data['phone']) ||
                !isset($data['address']) ||
                !isset($data['email']) 
            ) {
                return ['error' => 'some fields are required.'];
            }

            $stmt->bindParam(':fullnames', $data['fullnames']);
            $stmt->bindParam(':state_id', $data['state_id']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':email', $data['email']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new installer
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create Installer: ' . $e->getMessage()];
        }
    }
   public function updateInstallerPartial($id, $fields)
    {
        if (!$this->installerExists($id)) {
            return ['error' => 'Installer with the given ID does not exist'];
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

            $sql = "UPDATE installers SET $setClause WHERE id = :id";
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return ['error' => 'Failed to update installer record: ' . $e->getMessage()];
        }
    }

}
