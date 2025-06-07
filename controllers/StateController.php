<?php
require_once __DIR__ . '/../models/states.php';

class StateController
{
    private $state;

    public function __construct($db)
    {
        $this->state = new States($db);
    }
 public function getOne($id)
    {
        header('Content-Type: application/json');

        $state = $this->state->getStateById($id);

        if (!$state) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'state not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'state fetched successfully',
            'data' => $state
        ]);
    }
    public function getAll()
    {
        header('Content-Type: application/json');
        $allState = $this->state->getAllStates();

        if (empty($allState)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No state found',
                'data' => []
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'State fetched successfully',
            'data' => $allState
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        $result = $this->state->createState($input);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            echo json_encode([
                'status' => 201,
                'message' => 'State created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to create state'
            ]);
        }
    }
    public function delete($id)
    {
        header('Content-Type: application/json');

        // Attempt to delete the state post with the provided ID
        $result = $this->state->deleteState($id);

        // If deleteState returns an error (such as database issues or invalid ID)
        if (is_array($result) && isset($result['error'])) {
            // Log the error to ensure we know what went wrong in the backend (optional)
            error_log('Failed to delete state: ' . $result['error']);

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
                'message' => 'State deleted successfully'
            ]);
        }
        // If no record was found with the provided ID (or failed deletion)
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No state found with that ID'
            ]);
        }
    }
    // In the controller

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['state_name '])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Missing required fields: state_name'
            ]);
            return;
        }

        $result = $this->state->updateState($id, $data['state_name'], $data['slug']);

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
                'message' => 'State updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to update the State'
            ]);
        }
    }


}
