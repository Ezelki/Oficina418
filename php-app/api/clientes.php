<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Cliente.php';

$database = new Database();
$db = $database->getConnection();

$cliente = new Cliente($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(isset($_GET["id"])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id === false || $id === null) {
                http_response_code(400);
                echo json_encode(array("message" => "ID de cliente inválido."));
                exit();
            }
            $cliente->id = $id;
            $cliente->readOne();
            if($cliente->nome != null) {
                $cliente_arr = array(
                    "id" => $cliente->id,
                    "nome" => $cliente->nome,
                    "telefone" => $cliente->telefone,
                    "email" => $cliente->email
                );
                http_response_code(200);
                echo json_encode($cliente_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Cliente não encontrado."));
            }
        } else {
            $stmt = $cliente->readAll();
            $num = $stmt->rowCount();

            if($num > 0) {
                $clientes_arr = array();
                $clientes_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $cliente_item = array(
                        "id" => $id,
                        "nome" => $nome,
                        "telefone" => $telefone,
                        "email" => $email
                    );
                    array_push($clientes_arr["records"], $cliente_item);
                }

                http_response_code(200);
                echo json_encode($clientes_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("message" => "Nenhum cliente encontrado.", "records" => array()));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nome)) {
            $cliente->nome = $data->nome;
            $cliente->telefone = $data->telefone;
            $cliente->email = $data->email;

            if($cliente->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Cliente foi criado."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o cliente."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Não foi possível criar o cliente. Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        $cliente->id = $data->id;
        $cliente->nome = $data->nome;
        $cliente->telefone = $data->telefone;
        $cliente->email = $data->email;

        if($cliente->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Cliente foi atualizado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar o cliente."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        $cliente->id = $data->id;

        if($cliente->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Cliente foi deletado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível deletar o cliente."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>

