<?php

include("db.php");
include("header.php");
include("footer.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $placa = $_POST['placa'];
    $data = $_POST['data'];
    $mao_obra = $_POST['mao_obra'];

    // 🔍 Buscar o ID do veículo a partir da placa
    $sql_veiculo = "SELECT id FROM veiculos WHERE placa = ? AND cliente_id = ?";
    $stmt_veiculo = $pdo->prepare($sql_veiculo);
    $stmt_veiculo->execute([$placa, $cliente_id]);
    $veiculo = $stmt_veiculo->fetch();

    if (!$veiculo) {
        die("Erro: Veículo não encontrado para este cliente.");
    }

    $veiculo_id = $veiculo['id'];

    // ✅ Agora passamos veiculo_id corretamente
    $sql_servico = "INSERT INTO servicos (veiculo_id, data, mao_obra) VALUES (?, ?, ?)";
    $stmt_servico = $pdo->prepare($sql_servico);
    $stmt_servico->execute([$veiculo_id, $data, $mao_obra]);
    $servico_id = $pdo->lastInsertId();


    // Inserir os itens do serviço
    foreach ($_POST['itens'] as $item) {
        $descricao = $item['descricao'];
        $valor = $item['valor'];

        $sql_item = "INSERT INTO itens_servico (servico_id, descricao, valor) VALUES (?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);
        $stmt_item->execute([$servico_id, $descricao, $valor]);
    }

    echo "Serviço cadastrado com sucesso!";
}
?>

<form method="POST">
    Cliente:
    <select name="cliente_id">
        <?php
        $stmt = $pdo->query("SELECT id, nome FROM clientes");
        while ($cliente = $stmt->fetch()) {
            echo "<option value='{$cliente['id']}'>{$cliente['nome']}</option>";
        }
        ?>
    </select><br>

    Placa do Veículo: <input type="text" name="placa"><br>
    Data do Serviço: <input type="date" name="data"><br>
    Mão de Obra: <input type="number" name="mao_obra" step="0.01"><br>

    Itens do Serviço:
    <div id="itens">
        <div>
            Descrição: <input type="text" name="itens[0][descricao]">
            Valor: <input type="number" name="itens[0][valor]" step="0.01">
        </div>
    </div>
    <button type="submit">Cadastrar Serviço</button>
</form>

<script>
    // Adicionar novos itens dinamicamente
    document.querySelector('form').addEventListener('submit', function () {
        let itemIndex = document.querySelectorAll('#itens div').length;
        let newItem = document.createElement('div');
        newItem.innerHTML = `
            Descrição: <input type="text" name="itens[${itemIndex}][descricao]">
            Valor: <input type="number" name="itens[${itemIndex}][valor]" step="0.01">
        `;
        document.getElementById('itens').appendChild(newItem);
    });
</script>