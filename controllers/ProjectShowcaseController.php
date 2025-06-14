<?php
class ProjectShowcaseController
{
    private $projectShowcase;

    public function __construct($db)
    {
        $this->projectShowcase = new ProjectShowcase($db);
    }

    public function getByState($projectState)
    {
        header('Content-Type: application/json');

        if (!is_numeric($projectState)) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Invalid state ID'
            ]);
            return;
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        $page = max($page, 1);
        $limit = max($limit, 1);
        $offset = ($page - 1) * $limit;

        $result = $this->projectShowcase->getProjectsByState($projectState, $limit, $offset);

        $projects = $result['data'];
        $totalRows = $result['total'];

        if (empty($projects)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No projects found for this state',
                'data' => []
            ]);
            return;
        }

        $totalPages = ceil($totalRows / $limit);

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Project fetched successfully',
            'data' => $projects,
            'pagination' => [
                'total' => $totalRows,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }
    public function getStatesWithProjects()
    {
        $result = $this->projectShowcase->getStatesWithProjects();

        if ($result['status'] === 'success') {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'States with projects retrieved successfully',
                'data' => $result['data']
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'message' => 'An error occurred',
                'data' => []
            ]);
        }
    }
    public function getAll()
    {
        header('Content-Type: application/json');

        // Get pagination params from query string
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $page = max($page, 1);
        $limit = max($limit, 1);
        $offset = ($page - 1) * $limit;

        $result = $this->projectShowcase->getAll($limit, $offset);
        $projects = $result['data'];
        $totalRows = $result['total'];

        $totalPages = ceil($totalRows / $limit);

        http_response_code(200);

        echo json_encode([
            'status' => 200,
            'message' => 'Project showcase fetched successfully',
            'data' => $projects,
            'pagination' => [
                'total_rows' => $totalRows,
                'current_page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages,
            ]
        ]);
    }

    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['thumbnail']) || !isset($input['title']) || !isset($input['content']) || !isset($input['projectState'])) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'error' => 'Title, Thumbnail, projectState and Content are required']);
            return;
        }


        $result = $this->projectShowcase->create($input);

        if ($result) {
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => 'Project showcase created successfully',
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500, 'error' => 'Failed to create project showcase']);
        }
    }
    public function getById($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'error' => 'Invalid project ID']);
            return;
        }

        $project = $this->projectShowcase->getById($id);

        if (!$project) {
            http_response_code(404);
            echo json_encode(['status' => 404, 'error' => 'Project not found']);
            return;
        }

        echo json_encode(['status' => 201, 'data' => $project]);
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'error' => 'Invalid project ID']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $thumbnail = $input['thumbnail'] ?? null;
        $content = $input['content'] ?? null;
        $title = $input['title'] ?? null;
        $projectState = $input['projectState'] ?? null;

        if (!$thumbnail || !$content || !$title || !$projectState) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'error' => 'Title, projectState, Thumbnail and Content are required']);
            return;
        }

        $updated = $this->projectShowcase->update($id, $thumbnail, $title, $projectState, $content);

        if ($updated) {
            echo json_encode(['status' => 200, 'message' => 'Project updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500, 'error' => 'Failed to update project']);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'error' => 'Invalid project ID']);
            return;
        }

        $deleted = $this->projectShowcase->delete($id);

        if ($deleted) {
            echo json_encode(['status' => 200, 'message' => 'Project deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500, 'error' => 'Failed to delete project']);
        }
    }

    //Updating Contact Details

}
?>