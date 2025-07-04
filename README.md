# Sistema de Gestão de Oficina Mecânica

Um sistema web completo para gestão de oficina mecânica desenvolvido em PHP, MySQL e com suporte ao Docker.

## Funcionalidades

- **Gestão de Clientes**: Cadastro, edição e exclusão de clientes
- **Gestão de Veículos**: Cadastro de veículos vinculados aos clientes
- **Gestão de Orçamentos**: Criação e manipulação de orçamentos com itens detalhados
- **Dashboard**: Visão geral com estatísticas e últimos orçamentos
- **Interface Responsiva**: Design moderno e responsivo para desktop e mobile

## Tecnologias Utilizadas

- **Backend**: PHP 8.1 com PDO para conexão com banco de dados
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla) e Bootstrap 5
- **Banco de Dados**: MySQL 8.0
- **Servidor Web**: Apache 2.4
- **Containerização**: Docker e Docker Compose (opcional)

## Estrutura do Projeto

```
oficina-mecanica/
├── php-app/
│   ├── api/                    # APIs REST
│   │   ├── clientes.php
│   │   ├── veiculos.php
│   │   ├── orcamentos.php
│   │   └── itens_orcamento.php
│   ├── config/
│   │   └── database.php        # Configuração do banco
│   ├── models/                 # Modelos de dados
│   │   ├── Cliente.php
│   │   ├── Veiculo.php
│   │   ├── Orcamento.php
│   │   └── ItemOrcamento.php
│   ├── js/
│   │   └── app.js             # JavaScript principal
│   └── index.html             # Interface principal
├── mysql/
│   └── schema.sql             # Script de criação do banco
├── docker-compose.yml         # Configuração Docker
└── README.md
```

## Instalação e Configuração

### Opção 1: Instalação Local (Recomendada)

#### Pré-requisitos
- Apache 2.4+
- PHP 8.1+ com extensão PDO MySQL
- MySQL 8.0+

#### Passos de Instalação

1. **Clone ou copie o projeto**
   ```bash
   cp -r oficina-mecanica /var/www/html/
   ```

2. **Configure o Apache**
   ```bash
   sudo systemctl start apache2
   sudo systemctl enable apache2
   ```

3. **Configure o MySQL**
   ```bash
   sudo systemctl start mysql
   sudo systemctl enable mysql
   
   # Criar banco e usuário
   sudo mysql -e "CREATE DATABASE IF NOT EXISTS oficina_mecanica;"
   sudo mysql -e "CREATE USER IF NOT EXISTS 'user'@'localhost' IDENTIFIED BY 'password';"
   sudo mysql -e "GRANT ALL PRIVILEGES ON oficina_mecanica.* TO 'user'@'localhost';"
   sudo mysql -e "FLUSH PRIVILEGES;"
   ```

4. **Importar o schema do banco**
   ```bash
   sudo mysql oficina_mecanica < mysql/schema.sql
   ```

5. **Configurar permissões**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/
   ```

6. **Acessar o sistema**
   Abra o navegador e acesse: `http://localhost`

### Opção 2: Docker (Experimental)

```bash
cd oficina-mecanica
docker-compose up -d
```

**Nota**: A configuração Docker pode apresentar problemas em alguns ambientes devido a limitações do kernel.

## Configuração do Banco de Dados

O arquivo `config/database.php` contém as configurações de conexão:

```php
private $host = 'localhost';
private $db_name = 'oficina_mecanica';
private $username = 'user';
private $password = 'password';
```

Ajuste essas configurações conforme seu ambiente.

## Estrutura do Banco de Dados

### Tabelas

1. **clientes**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - nome (VARCHAR(255), NOT NULL)
   - telefone (VARCHAR(20))
   - email (VARCHAR(255))

2. **veiculos**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - marca (VARCHAR(100), NOT NULL)
   - modelo (VARCHAR(100), NOT NULL)
   - ano (INT)
   - placa (VARCHAR(10), UNIQUE, NOT NULL)
   - cliente_id (INT, FOREIGN KEY)

3. **orcamentos**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - veiculo_id (INT, FOREIGN KEY)
   - data_criacao (DATETIME, DEFAULT CURRENT_TIMESTAMP)
   - status (VARCHAR(50), DEFAULT 'Pendente')
   - valor_total (DECIMAL(10,2), DEFAULT 0.00)

4. **itens_orcamento**
   - id (INT, AUTO_INCREMENT, PRIMARY KEY)
   - orcamento_id (INT, FOREIGN KEY)
   - descricao (VARCHAR(255), NOT NULL)
   - quantidade (INT, NOT NULL)
   - valor_unitario (DECIMAL(10,2), NOT NULL)

## APIs Disponíveis

### Clientes
- `GET /api/clientes.php` - Listar todos os clientes
- `GET /api/clientes.php?id={id}` - Buscar cliente por ID
- `POST /api/clientes.php` - Criar novo cliente
- `PUT /api/clientes.php` - Atualizar cliente
- `DELETE /api/clientes.php` - Excluir cliente

### Veículos
- `GET /api/veiculos.php` - Listar todos os veículos
- `GET /api/veiculos.php?id={id}` - Buscar veículo por ID
- `POST /api/veiculos.php` - Criar novo veículo
- `PUT /api/veiculos.php` - Atualizar veículo
- `DELETE /api/veiculos.php` - Excluir veículo

### Orçamentos
- `GET /api/orcamentos.php` - Listar todos os orçamentos
- `GET /api/orcamentos.php?id={id}` - Buscar orçamento por ID
- `POST /api/orcamentos.php` - Criar novo orçamento
- `PUT /api/orcamentos.php` - Atualizar orçamento
- `DELETE /api/orcamentos.php` - Excluir orçamento

### Itens de Orçamento
- `GET /api/itens_orcamento.php?orcamento_id={id}` - Listar itens de um orçamento
- `GET /api/itens_orcamento.php?id={id}` - Buscar item por ID
- `POST /api/itens_orcamento.php` - Criar novo item
- `PUT /api/itens_orcamento.php` - Atualizar item
- `DELETE /api/itens_orcamento.php` - Excluir item

## Como Usar

### 1. Dashboard
- Visualize estatísticas gerais do sistema
- Veja os últimos orçamentos criados

### 2. Gestão de Clientes
- Clique em "Clientes" no menu
- Use "Novo Cliente" para adicionar clientes
- Edite ou exclua clientes usando os botões de ação

### 3. Gestão de Veículos
- Clique em "Veículos" no menu
- Use "Novo Veículo" para adicionar veículos
- Selecione o cliente proprietário do veículo

### 4. Gestão de Orçamentos
- Clique em "Orçamentos" no menu
- Use "Novo Orçamento" para criar orçamentos
- Selecione o veículo e adicione itens com descrição, quantidade e valor
- O valor total é calculado automaticamente

## Recursos da Interface

- **Design Responsivo**: Funciona em desktop, tablet e mobile
- **Navegação Intuitiva**: Menu superior para acesso rápido às seções
- **Modais**: Formulários em janelas modais para melhor experiência
- **Validação**: Validação de campos obrigatórios
- **Feedback Visual**: Alertas de sucesso e erro
- **Cálculo Automático**: Valor total dos orçamentos calculado automaticamente

## Solução de Problemas

### Erro de Conexão com Banco
1. Verifique se o MySQL está rodando: `sudo systemctl status mysql`
2. Confirme as credenciais em `config/database.php`
3. Verifique se o banco e usuário foram criados corretamente

### Erro 403 ou 404
1. Verifique as permissões dos arquivos: `sudo chown -R www-data:www-data /var/www/html/`
2. Confirme se o Apache está rodando: `sudo systemctl status apache2`

### APIs não funcionam
1. Verifique se o módulo PHP está habilitado no Apache
2. Confirme se a extensão PDO MySQL está instalada: `php -m | grep pdo_mysql`

## Melhorias Futuras

- Autenticação e autorização de usuários
- Relatórios em PDF
- Sistema de notificações
- Backup automático do banco de dados
- Integração com sistemas de pagamento
- App mobile nativo

## Suporte

Para dúvidas ou problemas, verifique:
1. Os logs do Apache: `/var/log/apache2/error.log`
2. Os logs do MySQL: `/var/log/mysql/error.log`
3. Console do navegador para erros JavaScript

## Licença

Este projeto foi desenvolvido para fins educacionais e pode ser usado livremente.

