<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Veiculo.php';

$database = new Database();
$db = $database->getConnection();

$veiculo = new Veiculo($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(!empty($_GET["id"])) {
            $veiculo->id = $_GET["id"];
            $veiculo->readOne();
            if($veiculo->marca != null) {
                $veiculo_arr = array(
                    "id" => $veiculo->id,
                    "marca" => $veiculo->marca,
                    "modelo" => $veiculo->modelo,
                    "ano" => $veiculo->ano,
                    "placa" => $veiculo->placa,
                    "cliente_id" => $veiculo->cliente_id
                );
                http_response_code(200);
                echo json_encode($veiculo_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Veículo não encontrado."));
            }
        } else {
            $stmt = $veiculo->readAll();
            $num = $stmt->rowCount();

            if($num > 0) {
                $veiculos_arr = array();
                $veiculos_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $veiculo_item = array(
                        "id" => $id,
                        "marca" => $marca,
                        "modelo" => $modelo,
                        "ano" => $ano,
                        "placa" => $placa,
                        "cliente_id" => $cliente_id,
                        "cliente_nome" => $cliente_nome
                    );
                    array_push($veiculos_arr["records"], $veiculo_item);
                }

                http_response_code(200);
                echo json_encode($veiculos_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array()));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->marca) && !empty($data->modelo) && !empty($data->placa) && !empty($data->cliente_id)) {
            $veiculo->marca = $data->marca;
            $veiculo->modelo = $data->modelo;
            $veiculo->ano = $data->ano;
            $veiculo->placa = $data->placa;
            $veiculo->cliente_id = $data->cliente_id;

            if($veiculo->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Veículo foi criado."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o veículo."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Não foi possível criar o veículo. Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        $veiculo->id = $data->id;
        $veiculo->marca = $data->marca;
        $veiculo->modelo = $data->modelo;
        $veiculo->ano = $data->ano;
        $veiculo->placa = $data->placa;
        $veiculo->cliente_id = $data->cliente_id;

        if($veiculo->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Veículo foi atualizado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar o veículo."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        $veiculo->id = $data->id;

        if($veiculo->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Veículo foi deletado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível deletar o veículo."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>

