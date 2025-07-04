<?php
class ConfiguracaoOficina {
    private $conn;
    private $table_name = "configuracoes_oficina";

    public $id;
    public $nome_oficina;
    public $endereco;
    public $telefone1;
    public $telefone2;
    public $email_contato;
    public $cnpj;
    public $logo_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT id, nome_oficina, endereco, telefone1, telefone2, email_contato, cnpj, logo_url FROM " . $this->table_name . " WHERE id = 1 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->nome_oficina = $row['nome_oficina'];
            $this->endereco = $row['endereco'];
            $this->telefone1 = $row['telefone1'];
            $this->telefone2 = $row['telefone2'];
            $this->email_contato = $row['email_contato'];
            $this->cnpj = $row['cnpj'];
            $this->logo_url = $row['logo_url'];
            return true;
        }
        return false;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                    nome_oficina = :nome_oficina, 
                    endereco = :endereco, 
                    telefone1 = :telefone1, 
                    telefone2 = :telefone2, 
                    email_contato = :email_contato, 
                    cnpj = :cnpj, 
                    logo_url = :logo_url 
                  WHERE id = 1";
        
        $stmt = $this->conn->prepare($query);

        $this->nome_oficina = htmlspecialchars(strip_tags($this->nome_oficina));
        $this->endereco = htmlspecialchars(strip_tags($this->endereco));
        $this->telefone1 = htmlspecialchars(strip_tags($this->telefone1));
        $this->telefone2 = htmlspecialchars(strip_tags($this->telefone2));
        $this->email_contato = htmlspecialchars(strip_tags($this->email_contato));
        $this->cnpj = htmlspecialchars(strip_tags($this->cnpj));
        $this->logo_url = htmlspecialchars(strip_tags($this->logo_url));

        $stmt->bindParam(':nome_oficina', $this->nome_oficina);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':telefone1', $this->telefone1);
        $stmt->bindParam(':telefone2', $this->telefone2);
        $stmt->bindParam(':email_contato', $this->email_contato);
        $stmt->bindParam(':cnpj', $this->cnpj);
        $stmt->bindParam(':logo_url', $this->logo_url);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET 
                    nome_oficina = :nome_oficina, 
                    endereco = :endereco, 
                    telefone1 = :telefone1, 
                    telefone2 = :telefone2, 
                    email_contato = :email_contato, 
                    cnpj = :cnpj, 
                    logo_url = :logo_url";
        
        $stmt = $this->conn->prepare($query);

        $this->nome_oficina = htmlspecialchars(strip_tags($this->nome_oficina));
        $this->endereco = htmlspecialchars(strip_tags($this->endereco));
        $this->telefone1 = htmlspecialchars(strip_tags($this->telefone1));
        $this->telefone2 = htmlspecialchars(strip_tags($this->telefone2));
        $this->email_contato = htmlspecialchars(strip_tags($this->email_contato));
        $this->cnpj = htmlspecialchars(strip_tags($this->cnpj));
        $this->logo_url = htmlspecialchars(strip_tags($this->logo_url));

        $stmt->bindParam(':nome_oficina', $this->nome_oficina);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':telefone1', $this->telefone1);
        $stmt->bindParam(':telefone2', $this->telefone2);
        $stmt->bindParam(':email_contato', $this->email_contato);
        $stmt->bindParam(':cnpj', $this->cnpj);
        $stmt->bindParam(':logo_url', $this->logo_url);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

