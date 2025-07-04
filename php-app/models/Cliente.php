<?php
class Cliente {
    private $conn;
    private $table_name = "clientes";

    public $id;
    public $nome;
    public $telefone;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome=:nome, telefone=:telefone, email=:email";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":email", $this->email);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function readAll() {
        $query = "SELECT id, nome, telefone, email FROM " . $this->table_name . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne() {
        $query = "SELECT id, nome, telefone, email FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nome = $row['nome'];
        $this->telefone = $row['telefone'];
        $this->email = $row['email'];
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET nome = :nome, telefone = :telefone, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':email', $this->email);
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

