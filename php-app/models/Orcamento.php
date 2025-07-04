<?php
class Orcamento {
    private $conn;
    private $table_name = "orcamentos";

    public $id;
    public $veiculo_id;
    public $data_criacao;
    public $status;
    public $valor_total;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET veiculo_id=:veiculo_id, status=:status, valor_total=:valor_total";
        $stmt = $this->conn->prepare($query);

        $this->veiculo_id = htmlspecialchars(strip_tags($this->veiculo_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->valor_total = htmlspecialchars(strip_tags($this->valor_total));

        $stmt->bindParam(":veiculo_id", $this->veiculo_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":valor_total", $this->valor_total);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    function readAll() {
        $query = "SELECT o.id, o.veiculo_id, o.data_criacao, o.status, o.valor_total, 
                         v.marca, v.modelo, v.placa, c.nome as cliente_nome 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN veiculos v ON o.veiculo_id = v.id 
                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                  ORDER BY o.data_criacao DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne() {
        $query = "SELECT o.id, o.veiculo_id, o.data_criacao, o.status, o.valor_total, 
                         v.marca, v.modelo, v.placa, c.nome as cliente_nome 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN veiculos v ON o.veiculo_id = v.id 
                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                  WHERE o.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->veiculo_id = $row['veiculo_id'];
        $this->data_criacao = $row['data_criacao'];
        $this->status = $row['status'];
        $this->valor_total = $row['valor_total'];
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET veiculo_id = :veiculo_id, status = :status, valor_total = :valor_total WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->veiculo_id = htmlspecialchars(strip_tags($this->veiculo_id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->valor_total = htmlspecialchars(strip_tags($this->valor_total));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':veiculo_id', $this->veiculo_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':valor_total', $this->valor_total);
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

    function updateTotal() {
        $query = "UPDATE " . $this->table_name . " SET valor_total = (
                    SELECT COALESCE(SUM(quantidade * valor_unitario), 0) 
                    FROM itens_orcamento 
                    WHERE orcamento_id = :id
                  ) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

