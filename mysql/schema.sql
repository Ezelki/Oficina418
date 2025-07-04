CREATE DATABASE IF NOT EXISTS oficina_mecanica;
USE oficina_mecanica;

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano INT,
    placa VARCHAR(10) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE IF NOT EXISTS orcamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pendente',
    valor_total DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id)
);

CREATE TABLE IF NOT EXISTS itens_orcamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orcamento_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (orcamento_id) REFERENCES orcamentos(id)
);


