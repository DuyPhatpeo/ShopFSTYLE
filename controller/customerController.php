<?php
// File: controller/InfoController.php
// -----------------------------------
require_once __DIR__ . '/../model/CustomerModel.php';
class InfoController {
    private $model;
    public function __construct($conn) {
        $this->model = new CustomerModel($conn);
        header('Content-Type: application/json');
    }
    public function handleRequest($customer_id) {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';
        switch ($method) {
            case 'GET':
                $this->getInfo($customer_id);
                break;
            case 'PUT':
                if ($action === 'update') $this->updateInfo($customer_id);
                elseif ($action === 'change_password') $this->changePassword($customer_id);
                else http_response_code(400);
                break;
            default:
                http_response_code(405);
        }
    }
    private function getInfo($id) {
        $c = $this->model->getById($id);
        if ($c) echo json_encode(['status'=>'success','data'=>$c]);
        else { http_response_code(404); echo json_encode(['status'=>'error','message'=>'Not found']); }
    }
    private function updateInfo($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $ok = $this->model->updateInfo($id, $data['full_name'], $data['phone'], $data['address']);
        echo json_encode(['status'=>$ok?'success':'error','message'=>$ok?'Updated':'Failed']);
    }
    private function changePassword($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $res = $this->model->changePassword($id, $data['old_password'], $data['new_password']);
        if ($res === true) echo json_encode(['status'=>'success','message'=>'Password changed']);
        elseif ($res === 'wrong_password') echo json_encode(['status'=>'error','message'=>'Old incorrect']);
        else echo json_encode(['status'=>'error','message'=>'Failed']);
    }
}

?>