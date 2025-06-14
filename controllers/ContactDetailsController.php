<?php

require_once __DIR__ . '/../models/ContactDetails.php';

class ContactDetailsController {
    private $model;

    public function __construct($db) {
        $this->model = new ContactDetails($db);
    }

    public function get() {
        $record = $this->model->get();
        if ($record) {
            echo json_encode(['status' => 200, 'data' => $record]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'No contact details found']);
        }
    }

    public function update() {
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->model->update($data)) {
            echo json_encode(['status' => 200, 'message' => 'Contact details updated']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500, 'message' => 'Failed to update contact details']);
        }
    }

    public function delete() {
        if ($this->model->delete()) {
            echo json_encode(['status' => 200, 'message' => 'Contact details deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500, 'message' => 'Failed to delete contact details']);
        }
    }
}
