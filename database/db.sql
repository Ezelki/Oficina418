USE oficina;

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    placa VARCHAR(10) NOT NULL,
    modelo VARCHAR(255),
    marca VARCHAR(255),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT,
    data DATE,
    mao_obra DECIMAL(10, 2),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS itens_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servico_id INT,
    descricao VARCHAR(255),
    valor DECIMAL(10, 2),
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE
);
