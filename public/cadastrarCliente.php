<?php

include("db.php");
include("header.php");
include("footer.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $placa = $_POST['placa'];
    $modelo = $_POST['modelo'];

    // Cadastrar o cliente
    $sql = "INSERT INTO clientes (nome, telefone, endereco) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $telefone, $endereco]);
    $cliente_id = $pdo->lastInsertId();

    // Cadastrar o veículo associado ao cliente
    if (!empty($placa)) {
        $sql_veiculo = "INSERT INTO veiculos (cliente_id, placa, modelo) VALUES (?, ?, ?)";
        $stmt_veiculo = $pdo->prepare($sql_veiculo);
        $stmt_veiculo->execute([$cliente_id, $placa, $modelo]);
    }

    echo "Cliente e veículo cadastrados com sucesso!";
}
?>

<form method="POST">
    <h3>Dados do Cliente</h3>
    Nome: <input type="text" name="nome" required><br>
    Telefone: <input type="text" name="telefone"><br>
    Endereço: <input type="text" name="endereco"><br>

    <h3>Dados do Veículo</h3>
    Placa: <input type="text" name="placa"><br>
    Modelo: <input type="text" name="modelo"><br>

    <button type="submit">Cadastrar Cliente e Veículo</button>
</form>
