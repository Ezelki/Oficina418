<?php

include("db.php");
include("header.php");
include("footer.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    $sql = "INSERT INTO clientes (nome, telefone, endereco) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $telefone, $endereco]);

    echo "Cliente cadastrado com sucesso!";
}
?>

<form method="POST">
    Nome: <input type="text" name="nome" required><br>
    Telefone: <input type="text" name="telefone"><br>
    Endereço: <input type="text" name="endereco"><br>
    <button type="submit">Cadastrar Cliente</button>
</form>
