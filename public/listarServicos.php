<?php

include("db.php");
include("header.php");
include("footer.php");

$stmt = $pdo->query("SELECT s.id, c.nome AS cliente, v.placa, s.data, s.mao_obra 
                     FROM servicos s
                     JOIN veiculos v ON v.id = s.veiculo_id
                     JOIN clientes c ON c.id = v.cliente_id");
?>
<table>
    <tr>
        <th>Cliente</th>
        <th>Placa</th>
        <th>Data</th>
        <th>Serviços</th>
        <th>Valor Total</th>
    </tr>
    <?php while ($servico = $stmt->fetch()): ?>
        <tr>
            <td><?php echo $servico['cliente']; ?></td>
            <td><?php echo $servico['placa']; ?></td>
            <td><?php echo $servico['data']; ?></td>
            <td>
                <?php
                // Itens do serviço
                $sql_itens = "SELECT descricao, valor FROM itens_servico WHERE servico_id = ?";
                $stmt_itens = $pdo->prepare($sql_itens);
                $stmt_itens->execute([$servico['id']]);
                $total = $servico['mao_obra'];
                while ($item = $stmt_itens->fetch()) {
                    echo $item['descricao'] . " - R$ " . number_format($item['valor'], 2, ',', '.') . "<br>";
                    $total += $item['valor'];
                }
                ?>
            </td>
            <td>R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
        </tr>
    <?php endwhile; ?>
</table>
