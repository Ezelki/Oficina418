<?php
$dsn = "mysql:host=db;dbname=oficina;charset=utf8";
$user = "ezequiel";
$password = "ezedbquiel";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conectado com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}

?>
