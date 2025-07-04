<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Orcamento.php';

$database = new Database();
$db = $database->getConnection();

$orcamento = new Orcamento($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(!empty($_GET["id"])) {
            $orcamento->id = $_GET["id"];
            $orcamento->readOne();
            if($orcamento->veiculo_id != null) {
                $orcamento_arr = array(
                    "id" => $orcamento->id,
                    "veiculo_id" => $orcamento->veiculo_id,
                    "data_criacao" => $orcamento->data_criacao,
                    "status" => $orcamento->status,
                    "valor_total" => $orcamento->valor_total
                );
                http_response_code(200);
                echo json_encode($orcamento_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Orçamento não encontrado."));
            }
        } else {
            $stmt = $orcamento->readAll();
            $num = $stmt->rowCount();

            if($num > 0) {
                $orcamentos_arr = array();
                $orcamentos_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $orcamento_item = array(
                        "id" => $id,
                        "veiculo_id" => $veiculo_id,
                        "data_criacao" => $data_criacao,
                        "status" => $status,
                        "valor_total" => $valor_total,
                        "marca" => $marca,
                        "modelo" => $modelo,
                        "placa" => $placa,
                        "cliente_nome" => $cliente_nome
                    );
                    array_push($orcamentos_arr["records"], $orcamento_item);
                }

                http_response_code(200);
                echo json_encode($orcamentos_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array()));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->veiculo_id)) {
            $orcamento->veiculo_id = $data->veiculo_id;
            $orcamento->status = isset($data->status) ? $data->status : 'Pendente';
            $orcamento->valor_total = isset($data->valor_total) ? $data->valor_total : 0.00;

            if($orcamento->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Orçamento foi criado.", "id" => $orcamento->id));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o orçamento."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Não foi possível criar o orçamento. Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        $orcamento->id = $data->id;
        $orcamento->veiculo_id = $data->veiculo_id;
        $orcamento->status = $data->status;
        $orcamento->valor_total = $data->valor_total;

        if($orcamento->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Orçamento foi atualizado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar o orçamento."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        $orcamento->id = $data->id;

        if($orcamento->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Orçamento foi deletado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível deletar o orçamento."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>

