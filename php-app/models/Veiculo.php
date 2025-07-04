<?php
class Veiculo {
    private $conn;
    private $table_name = "veiculos";

    public $id;
    public $marca;
    public $modelo;
    public $ano;
    public $placa;
    public $cliente_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET marca=:marca, modelo=:modelo, ano=:ano, placa=:placa, cliente_id=:cliente_id";
        $stmt = $this->conn->prepare($query);

        $this->marca = htmlspecialchars(strip_tags($this->marca));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->ano = htmlspecialchars(strip_tags($this->ano));
        $this->placa = htmlspecialchars(strip_tags($this->placa));
        $this->cliente_id = htmlspecialchars(strip_tags($this->cliente_id));

        $stmt->bindParam(":marca", $this->marca);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":ano", $this->ano);
        $stmt->bindParam(":placa", $this->placa);
        $stmt->bindParam(":cliente_id", $this->cliente_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function readAll() {
        $query = "SELECT v.id, v.marca, v.modelo, v.ano, v.placa, v.cliente_id, c.nome as cliente_nome 
                  FROM " . $this->table_name . " v 
                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                  ORDER BY v.marca, v.modelo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne() {
        $query = "SELECT v.id, v.marca, v.modelo, v.ano, v.placa, v.cliente_id, c.nome as cliente_nome 
                  FROM " . $this->table_name . " v 
                  LEFT JOIN clientes c ON v.cliente_id = c.id 
                  WHERE v.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->marca = $row['marca'];
        $this->modelo = $row['modelo'];
        $this->ano = $row['ano'];
        $this->placa = $row['placa'];
        $this->cliente_id = $row['cliente_id'];
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET marca = :marca, modelo = :modelo, ano = :ano, placa = :placa, cliente_id = :cliente_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->marca = htmlspecialchars(strip_tags($this->marca));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->ano = htmlspecialchars(strip_tags($this->ano));
        $this->placa = htmlspecialchars(strip_tags($this->placa));
        $this->cliente_id = htmlspecialchars(strip_tags($this->cliente_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':marca', $this->marca);
        $stmt->bindParam(':modelo', $this->modelo);
        $stmt->bindParam(':ano', $this->ano);
        $stmt->bindParam(':placa', $this->placa);
        $stmt->bindParam(':cliente_id', $this->cliente_id);
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

