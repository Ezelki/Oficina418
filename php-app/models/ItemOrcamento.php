<?php
class ItemOrcamento {
    private $conn;
    private $table_name = "itens_orcamento";

    public $id;
    public $orcamento_id;
    public $descricao;
    public $quantidade;
    public $valor_unitario;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET orcamento_id=:orcamento_id, descricao=:descricao, quantidade=:quantidade, valor_unitario=:valor_unitario";
        $stmt = $this->conn->prepare($query);

        $this->orcamento_id = htmlspecialchars(strip_tags($this->orcamento_id));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->valor_unitario = htmlspecialchars(strip_tags($this->valor_unitario));

        $stmt->bindParam(":orcamento_id", $this->orcamento_id);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":quantidade", $this->quantidade);
        $stmt->bindParam(":valor_unitario", $this->valor_unitario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function readByOrcamento($orcamento_id) {
        $query = "SELECT id, orcamento_id, descricao, quantidade, valor_unitario FROM " . $this->table_name . " WHERE orcamento_id = ? ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $orcamento_id);
        $stmt->execute();
        return $stmt;
    }

    function readOne() {
        $query = "SELECT id, orcamento_id, descricao, quantidade, valor_unitario FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->orcamento_id = $row['orcamento_id'];
        $this->descricao = $row['descricao'];
        $this->quantidade = $row['quantidade'];
        $this->valor_unitario = $row['valor_unitario'];
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET orcamento_id = :orcamento_id, descricao = :descricao, quantidade = :quantidade, valor_unitario = :valor_unitario WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->orcamento_id = htmlspecialchars(strip_tags($this->orcamento_id));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->quantidade = htmlspecialchars(strip_tags($this->quantidade));
        $this->valor_unitario = htmlspecialchars(strip_tags($this->valor_unitario));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':orcamento_id', $this->orcamento_id);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':quantidade', $this->quantidade);
        $stmt->bindParam(':valor_unitario', $this->valor_unitario);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

