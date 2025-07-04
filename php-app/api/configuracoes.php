<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/ConfiguracaoOficina.php';

$database = new Database();
$db = $database->getConnection();

$configuracao = new ConfiguracaoOficina($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if($configuracao->read()) {
            $configuracao_arr = array(
                "id" => $configuracao->id,
                "nome_oficina" => $configuracao->nome_oficina,
                "endereco" => $configuracao->endereco,
                "telefone1" => $configuracao->telefone1,
                "telefone2" => $configuracao->telefone2,
                "email_contato" => $configuracao->email_contato,
                "cnpj" => $configuracao->cnpj,
                "logo_url" => $configuracao->logo_url
            );
            http_response_code(200);
            echo json_encode($configuracao_arr);
        } else {
            // Se não existe configuração, criar uma padrão
            $configuracao->nome_oficina = "Minha Oficina";
            $configuracao->endereco = "";
            $configuracao->telefone1 = "";
            $configuracao->telefone2 = "";
            $configuracao->email_contato = "";
            $configuracao->cnpj = "";
            $configuracao->logo_url = "";
            
            if($configuracao->create()) {
                $configuracao->read();
                $configuracao_arr = array(
                    "id" => $configuracao->id,
                    "nome_oficina" => $configuracao->nome_oficina,
                    "endereco" => $configuracao->endereco,
                    "telefone1" => $configuracao->telefone1,
                    "telefone2" => $configuracao->telefone2,
                    "email_contato" => $configuracao->email_contato,
                    "cnpj" => $configuracao->cnpj,
                    "logo_url" => $configuracao->logo_url
                );
                http_response_code(200);
                echo json_encode($configuracao_arr);
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível carregar as configurações."));
            }
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        $configuracao->nome_oficina = $data->nome_oficina;
        $configuracao->endereco = $data->endereco;
        $configuracao->telefone1 = $data->telefone1;
        $configuracao->telefone2 = $data->telefone2;
        $configuracao->email_contato = $data->email_contato;
        $configuracao->cnpj = $data->cnpj;
        $configuracao->logo_url = $data->logo_url;

        if($configuracao->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Configurações foram atualizadas."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível atualizar as configurações."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>

