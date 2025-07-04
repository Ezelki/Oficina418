<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Orcamento.php';
include_once '../models/ItemOrcamento.php';
include_once '../models/ConfiguracaoOficina.php';

$database = new Database();
$db = $database->getConnection();

$orcamento = new Orcamento($db);
$item = new ItemOrcamento($db);
$configuracao = new ConfiguracaoOficina($db);

if (!empty($_GET["id"])) {
    $orcamento->id = $_GET["id"];
    $orcamento->readOne();
    
    if ($orcamento->veiculo_id != null) {
        // Buscar dados do orçamento completo
        $query = "SELECT o.id, o.veiculo_id, o.data_criacao, o.status, o.valor_total, 
                         v.marca, v.modelo, v.placa, v.ano, c.nome as cliente_nome, c.telefone, c.email 
                  FROM orcamentos o 
                  LEFT JOIN veiculos v ON o.veiculo_id = v.id 
                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                  WHERE o.id = ? LIMIT 0,1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $orcamento->id);
        $stmt->execute();
        $orcamento_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Buscar itens do orçamento
        $stmt_itens = $item->readByOrcamento($orcamento->id);
        $itens = array();
        while ($row = $stmt_itens->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = $row;
        }
        
        // Buscar configurações da oficina
        $configuracao->read();
        
        $response = array(
            "orcamento" => $orcamento_data,
            "itens" => $itens,
            "configuracao" => array(
                "nome_oficina" => $configuracao->nome_oficina,
                "endereco" => $configuracao->endereco,
                "telefone1" => $configuracao->telefone1,
                "telefone2" => $configuracao->telefone2,
                "email_contato" => $configuracao->email_contato,
                "cnpj" => $configuracao->cnpj,
                "logo_url" => $configuracao->logo_url
            )
        );
        
        http_response_code(200);
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Orçamento não encontrado."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "ID do orçamento é obrigatório."));
}
?>

