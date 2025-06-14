<?php
class States
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function stateExists($id)
    {
        $sql = "SELECT id FROM states  WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;

    }
    // Fetch all states
    public function getAllStates()
    {
        try {
            $sql = "SELECT * FROM states ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $categories;
        } catch (PDOException $e) {
            // Log or handle the error appropriately
            return ['error' => 'Failed to fetch states: ' . $e->getMessage()];
        }
    }

    public function getStatesWithProjects()
    {
        try {
            $query = "SELECT DISTINCT 
                s.id, 
                s.state_name, 
                s.state_logo
            FROM 
                states s
            INNER JOIN 
                project_showcases ps ON s.id = ps.projectState
            ORDER BY 
                s.state_name ASC
        ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $states
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
    public function getStatesWithInstallers()
    {
        try {
            $query = "SELECT DISTINCT 
                s.id, 
                s.state_name, 
                s.state_logo
            FROM 
                states s
            INNER JOIN 
                installers ps ON s.id = ps.state_id
            ORDER BY 
                s.state_name ASC
        ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $states
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }


    public function deleteState($id)
    {
        try {
            // Prepare the delete query
            $sql = "DELETE FROM states WHERE id = :id";
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
    public function getStateById($id)
    {
        try {
            $sql = "SELECT 
                    *
                FROM states 
                LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $state = $stmt->fetch(PDO::FETCH_ASSOC);
            return $state ?: null;
        } catch (PDOException $e) {
            return ['error' => 'Failed to fetch state: ' . $e->getMessage()];
        }
    }
    public function createState($data)
    {
        try {
            $sql = "INSERT INTO states (state_name, state_logo, created_at) VALUES (:state_name, :state_logo, NOW())";
            $stmt = $this->conn->prepare($sql);

            if (
                !isset($data['state_name']) ||
                !isset($data['state_logo'])
            ) {
                return ['error' => 'missing required fields (state name and state_logo)'];
            }

            $stmt->bindParam(':state_name', $data['state_name']);
            $stmt->bindParam(':state_logo', $data['state_logo']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Return the ID of the new state
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create State: ' . $e->getMessage()];
        }
    }
    public function updateState($id, $state_name, $state_logo)
    {
        $result = $this->stateExists($id);



        if (!$result) {
            return ['error' => 'State with the given ID does not exist'];
        }
        try {
            $sql = "UPDATE states SET state_name = :state_name, state_logo = :state_logo, WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':state_name', $state_name);
            $stmt->bindParam(':state_logo', $state_logo);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to update states: ' . $e->getMessage()];
        }
    }
}
