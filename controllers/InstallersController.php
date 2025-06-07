<?php
require_once __DIR__ . '/../models/Installer.php';

class InstallersController
{
    private $installer;

    public function __construct($db)
    {
        $this->installer = new Installer($db);
    }

    public function getByState($stateId)
{
    header('Content-Type: application/json');

    if (!is_numeric($stateId)) {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'error' => 'Invalid state ID'
        ]);
        return;
    }

    $installers = $this->installer->getInstallerByState($stateId);

    if (empty($installers)) {
        http_response_code(404);
        echo json_encode([
            'status' => 404,
            'message' => 'No Installers found for this state',
            'data' => []
        ]);
        return;
    }

    http_response_code(200);
    echo json_encode([
        'status' => 200,
        'message' => 'Installers fetched successfully',
        'data' => $installers
    ]);
}


    public function getOne($id)
    {
        header('Content-Type: application/json');

        $installer = $this->installer->getInstallerById($id);

        if (!$installer) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'Installer not found',
                'data' => null
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Installer fetched successfully',
            'data' => $installer
        ]);
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        $allInstallers = $this->installer->getAllInstallers();

        if (empty($allInstallers)) {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No Installer found',
                'data' => []
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Installers fetched successfully',
            'data' => $allInstallers
        ]);
    }
    public function create()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        $result = $this->installer->createInstaller($input);

        if (is_array($result) && isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            echo json_encode([
                'status' => 201,
                'message' => 'Installer created successfully',
                'id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => 'Failed to create Installer record'
            ]);
        }
    }
    public function delete($id)
    {
        header('Content-Type: application/json');

        // Attempt to delete the installer with the provided ID
        $result = $this->installer->deleteInstaller($id);

        // If deleteInstaller returns an error (such as database issues or invalid ID)
        if (is_array($result) && isset($result['error'])) {
            // Log the error to ensure we know what went wrong in the backend (optional)
            error_log('Failed to delete installer: ' . $result['error']);

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
                'message' => 'Installer deleted successfully'
            ]);
        }
        // If no record was found with the provided ID (or failed deletion)
        else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'message' => 'No Installer found with that ID'
            ]);
        }
    }
    // In the controller
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'Invalid instaler ID'
            ]);
            return;
        }

        // Pass only valid fields for update
        $allowedFields = [
            'fullnames',
            'state_id',
            'email',
            'phone',
            'address'
        ];

        $fieldsToUpdate = [];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fieldsToUpdate[$field] = $data[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'error' => 'No valid fields provided for update'
            ]);
            return;
        }

        // Call the model method with only fields that should be updated
        $result = $this->installer->updateInstallerPartial($id, $fieldsToUpdate);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'error' => $result['error']
            ]);
        } elseif ($result) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => 'Installer updated successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 404,
                'error' => 'Installer not found or not updated'
            ]);
        }
    }
}
