<?php
class ProjectShowcase
{
    private $conn;
    private $table = 'project_showcases';

    public $id;
    public $thumbnail;
    public $content;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function projectExists($id)
    {
        $sql = "SELECT id FROM project_showcases WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getProjectsByState($stateId, $limit, $offset)
    {
        try {
            // Main query to fetch projects for a specific state
            $query = "SELECT 
                ps.*, 
                s.state_name, 
                s.state_logo 
            FROM 
                project_showcases ps
            JOIN 
                states s ON ps.projectState = s.id
            WHERE 
                ps.projectState = :stateId
            ORDER BY 
                ps.created_at DESC
            LIMIT :limit OFFSET :offset";

            // Prepare and bind values
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':stateId', $stateId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Count query to get total number of projects for the state
            $countSql = "SELECT COUNT(*) as total FROM project_showcases WHERE projectState = :stateId";
            $countStmt = $this->conn->prepare($countSql);
            $countStmt->bindValue(':stateId', $stateId, PDO::PARAM_INT);
            $countStmt->execute();
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $countResult['total'] ?? 0
            ];

        } catch (PDOException $e) {
            return [
                'data' => [],
                'total' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    // Fetch all project showcases
    public function getAll($limit, $offset)
    {
        try {
            // Get total count first
            $countQuery = "SELECT COUNT(*) as total FROM {$this->table}";
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->execute();
            $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Fetch paginated data with state details
            $query = "SELECT 
                    ps.*, 
                    s.state_name, 
                    s.state_logo 
                FROM {$this->table} ps
                LEFT JOIN states s ON ps.projectState = s.id
                ORDER BY ps.created_at DESC
                LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();

            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'total' => $total,
                'data' => $projects
            ];

        } catch (PDOException $e) {
            return [
                'total' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public function getStatesWithProjects()
    {
        try {
            $query = "SELECT 
                    s.id, 
                    s.state_name, 
                    s.state_logo
                  FROM 
                    states s
                  JOIN 
                    project_showcases ps ON ps.projectState = s.id
                  GROUP BY 
                    s.id, s.state_name, s.state_logo";

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


    public function create($data)
    {
        try {
            $query = "INSERT INTO project_showcases (thumbnail, title, content, projectState, created_at, updated_at)
                  VALUES (:thumbnail, :title, :content, :projectState, NOW(), NOW())";

            $stmt = $this->conn->prepare($query);

            if (
                !isset($data['thumbnail']) ||
                !isset($data['title']) ||
                !isset($data['projectState']) ||
                !isset($data['content'])
            ) {
                return ['error' => 'some fields are required.'];
            }

            $thumbnail = htmlspecialchars(strip_tags($data['thumbnail']));
            $title = htmlspecialchars(strip_tags($data['title']));
            $projectState = htmlspecialchars(strip_tags($data['projectState']));
            $content = htmlspecialchars(strip_tags($data['content']));

            $stmt->bindParam(':thumbnail', $thumbnail);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':projectState', $projectState);
            $stmt->bindParam(':content', $content);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to create Product: ' . $e->getMessage()];
        }
    }

    // Fetch single project by id
    public function getById($id)
    {

        try {
            $query = "SELECT 
            ps.*, 
            s.state_name, 
            s.state_logo 
          FROM {$this->table} ps
          LEFT JOIN states s ON ps.projectState = s.id
          WHERE ps.id = :id
          LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($project) {
                return $project;
            } else {
                return [
                    'status' => '404',
                    'message' => 'Project not found',
                    'data' => null
                ];
            }

        } catch (PDOException $e) {
            return [
                'status' => '501',
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }


    // Update project showcase by id
    public function update($id, $thumbnail, $title, $projectState, $content)
    {

        $result = $this->projectExists(($id));

        if (!$result) {
            return ['error' => 'Project Showcase with the given ID does not exist'];
        }

        try {
            $query = "UPDATE {$this->table} 
                  SET thumbnail = :thumbnail, title =:title, projectState =:projectState, content = :content, updated_at = NOW()
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':thumbnail', $thumbnail);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':projectState', $projectState);
            $stmt->bindParam(':title', $title);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return ['error' => 'Failed to update project: ' . $e->getMessage()];
        }
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        //return $stmt->execute();
    }

    // Delete project showcase by id
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>