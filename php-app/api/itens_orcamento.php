<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/ItemOrcamento.php';
include_once '../models/Orcamento.php';

$database = new Database();
$db = $database->getConnection();

$item = new ItemOrcamento($db);
$orcamento = new Orcamento($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(!empty($_GET["orcamento_id"])) {
            $stmt = $item->readByOrcamento($_GET["orcamento_id"]);
            $num = $stmt->rowCount();

            if($num > 0) {
                $itens_arr = array();
                $itens_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $item_obj = array(
                        "id" => $id,
                        "orcamento_id" => $orcamento_id,
                        "descricao" => $descricao,
                        "quantidade" => $quantidade,
                        "valor_unitario" => $valor_unitario
                    );
                    array_push($itens_arr["records"], $item_obj);
                }

                http_response_code(200);
                echo json_encode($itens_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array()));
            }
        } else if(!empty($_GET["id"])) {
            $item->id = $_GET["id"];
            $item->readOne();
            if($item->descricao != null) {
                $item_arr = array(
                    "id" => $item->id,
                    "orcamento_id" => $item->orcamento_id,
                    "descricao" => $item->descricao,
                    "quantidade" => $item->quantidade,
                    "valor_unitario" => $item->valor_unitario
                );
                http_response_code(200);
                echo json_encode($item_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Item não encontrado."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Parâmetro orcamento_id ou id é obrigatório."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->orcamento_id) && !empty($data->descricao) && !empty($data->quantidade) && !empty($data->valor_unitario)) {
            $item->orcamento_id = $data->orcamento_id;
            $item->descricao = $data->descricao;
            $item->quantidade = $data->quantidade;
            $item->valor_unitario = $data->valor_unitario;

            if($item->create()) {
                // Atualizar o valor total do orçamento
                $orcamento->id = $data->orcamento_id;
                $orcamento->updateTotal();
                
                http_response_code(201);
                echo json_encode(array("message" => "Item foi criado."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o item."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Não foi possível criar o item. Dados incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        $item->id = $data->id;
        $item->orcamento_id = $data->orcamento_id;
        $item->descricao = $data->descricao;
        $item->quantidade = $data->quantidade;
        $item->valor_unitario = $data->valor_unitario;

        if($item->update()) {
            // Atualizar o valor total do orçamento
            $orcamento->id = $data->orcamento_id;
            $orcamento->updateTotal();
            
            http_response_code(200);
            echo json_encode(array("message" => "Item foi atualizado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar o item."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        $item->id = $data->id;
        $item->readOne(); // Para obter o orcamento_id
        $orcamento_id = $item->orcamento_id;

        if($item->delete()) {
            // Atualizar o valor total do orçamento
            $orcamento->id = $orcamento_id;
            $orcamento->updateTotal();
            
            http_response_code(200);
            echo json_encode(array("message" => "Item foi deletado."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível deletar o item."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>

