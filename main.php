<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With, Origin, Content-Type");
header("Access-Control-Max-Age: 86400");

date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

$root = $_SERVER['DOCUMENT_ROOT'];
$api = $root . '/CRUD-PHP-API';

require_once($api . '/configs/connection.php');
require_once($api . '/models/crud.model.php');

$dbase = new connection();
$pdo = $dbase->connect();

$crud = new Crud_model($pdo);

$data = json_decode(file_get_contents("php://input"));

$req = [];

if (isset($_REQUEST['request'])) {
    $req = explode('/', rtrim($_REQUEST['request'], '/'));
} else {
    $req = array('errorcatcher');
}

switch ($_SERVER['REQUEST_METHOD']) {
    // GET ALL USERS AND GET ONE USER
    case 'GET':
        if ($req[0] == 'Get') {
            if ($req[1] == 'All') {
                echo json_encode($crud->getAll());
                return;
            }
            if ($req[1] == 'One') {
                echo json_encode($crud->getOne($data));
                return;
            }
        }
        break;

    // INSERTING SINGLE USER
    case 'POST':
        if ($req[0] == 'Insert') {
            echo json_encode($crud->insert($data));
            return;
        }
        break;

    // UPDATING SINGLE USER
    case 'PUT':
        if ($req[0] == 'Update') {
            echo json_encode($crud->update($data));
            return;
        }
        break;

    // DELETING SINGLE USER
    case 'DELETE':
        if ($req[0] == 'Delete') {
            echo json_encode($crud->delete($data));
            return;
        }
        echo json_encode(["message" => "Endpoint does not exist"]);
        http_response_code(403);
        break;

    default:
        echo "Invalid HTTP Request";
        http_response_code(403);
        break;
}
