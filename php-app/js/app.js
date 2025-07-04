// Configuração da API
const API_BASE = '/api';

// Variáveis globais
let currentOrcamentoId = null;
let itensOrcamento = [];

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    showSection('dashboard');
    loadDashboard();
});

// Navegação entre seções
function showSection(section) {
    // Esconder todas as seções
    document.querySelectorAll('.section').forEach(el => {
        el.style.display = 'none';
    });
    
    // Mostrar seção selecionada
    document.getElementById(section + '-section').style.display = 'block';
    
    // Carregar dados da seção
    switch(section) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'clientes':
            loadClientes();
            break;
        case 'veiculos':
            loadVeiculos();
            break;
        case 'orcamentos':
            loadOrcamentos();
            break;
        case 'configuracoes':
            loadConfiguracoes();
            break;
    }
}

// Dashboard
async function loadDashboard() {
    try {
        const [clientes, veiculos, orcamentos] = await Promise.all([
            fetch(`${API_BASE}/clientes.php`).then(r => r.json()),
            fetch(`${API_BASE}/veiculos.php`).then(r => r.json()),
            fetch(`${API_BASE}/orcamentos.php`).then(r => r.json())
        ]);

        document.getElementById('total-clientes').textContent = clientes.records?.length || 0;
        document.getElementById('total-veiculos').textContent = veiculos.records?.length || 0;
        document.getElementById('total-orcamentos').textContent = orcamentos.records?.length || 0;

        const valorTotal = orcamentos.records?.reduce((sum, orc) => sum + parseFloat(orc.valor_total || 0), 0) || 0;
        document.getElementById('valor-total').textContent = `R$ ${valorTotal.toFixed(2)}`;

        // Últimos orçamentos
        const tbody = document.getElementById('dashboard-orcamentos');
        tbody.innerHTML = '';
        
        if (orcamentos.records && orcamentos.records.length > 0) {
            orcamentos.records.slice(0, 5).forEach(orc => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${orc.id}</td>
                    <td>${orc.cliente_nome || 'N/A'}</td>
                    <td>${orc.marca} ${orc.modelo} - ${orc.placa}</td>
                    <td>${new Date(orc.data_criacao).toLocaleDateString('pt-BR')}</td>
                    <td><span class="badge bg-${getStatusColor(orc.status)}">${orc.status}</span></td>
                    <td>R$ ${parseFloat(orc.valor_total).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar dashboard:', error);
    }
}

// Clientes
async function loadClientes() {
    try {
        const response = await fetch(`${API_BASE}/clientes.php`);
        const data = await response.json();
        
        allClientes = data.records || data || [];
        displayClientes(allClientes);
    } catch (error) {
        console.error('Erro ao carregar clientes:', error);
    }
}

function showClienteModal(id = null) {
    document.getElementById('clienteForm').reset();
    document.getElementById('cliente-id').value = '';
    
    if (id) {
        loadClienteData(id);
    }
    
    new bootstrap.Modal(document.getElementById('clienteModal')).show();
}

async function loadClienteData(id) {
    try {
        const response = await fetch(`${API_BASE}/clientes.php?id=${id}`);
        const cliente = await response.json();
        
        document.getElementById('cliente-id').value = cliente.id;
        document.getElementById('cliente-nome').value = cliente.nome;
        document.getElementById('cliente-telefone').value = cliente.telefone || '';
        document.getElementById('cliente-email').value = cliente.email || '';
    } catch (error) {
        console.error('Erro ao carregar cliente:', error);
    }
}

async function saveCliente() {
    const id = document.getElementById('cliente-id').value;
    const nome = document.getElementById('cliente-nome').value;
    const telefone = document.getElementById('cliente-telefone').value;
    const email = document.getElementById('cliente-email').value;
    
    if (!nome) {
        alert('Nome é obrigatório');
        return;
    }
    
    const data = { nome, telefone, email };
    
    try {
        let response;
        if (id) {
            data.id = id;
            response = await fetch(`${API_BASE}/clientes.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            response = await fetch(`${API_BASE}/clientes.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        
        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('clienteModal')).hide();
            loadClientes();
            showAlert('Cliente salvo com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar cliente', 'danger');
        }
    } catch (error) {
        console.error('Erro ao salvar cliente:', error);
        showAlert('Erro ao salvar cliente', 'danger');
    }
}

function editCliente(id) {
    showClienteModal(id);
}

async function deleteCliente(id) {
    if (!confirm('Tem certeza que deseja excluir este cliente?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/clientes.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        if (response.ok) {
            loadClientes();
            showAlert('Cliente excluído com sucesso!', 'success');
        } else {
            showAlert('Erro ao excluir cliente', 'danger');
        }
    } catch (error) {
        console.error('Erro ao excluir cliente:', error);
        showAlert('Erro ao excluir cliente', 'danger');
    }
}

// Veículos
async function loadVeiculos() {
    try {
        const response = await fetch(`${API_BASE}/veiculos.php`);
        const data = await response.json();
        
        allVeiculos = data.records || data || [];
        displayVeiculos(allVeiculos);
    } catch (error) {
        console.error('Erro ao carregar veículos:', error);
    }
}

async function showVeiculoModal(id = null) {
    document.getElementById('veiculoForm').reset();
    document.getElementById('veiculo-id').value = '';
    
    // Carregar clientes para o select
    await loadClientesSelect();
    
    if (id) {
        loadVeiculoData(id);
    }
    
    new bootstrap.Modal(document.getElementById('veiculoModal')).show();
}

async function loadClientesSelect() {
    try {
        const response = await fetch(`${API_BASE}/clientes.php`);
        const data = await response.json();
        
        const select = document.getElementById('veiculo-cliente');
        select.innerHTML = '<option value="">Selecione um cliente</option>';
        
        if (data.records && data.records.length > 0) {
            data.records.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = cliente.nome;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar clientes:', error);
    }
}

async function loadVeiculoData(id) {
    try {
        const response = await fetch(`${API_BASE}/veiculos.php?id=${id}`);
        const veiculo = await response.json();
        
        document.getElementById('veiculo-id').value = veiculo.id;
        document.getElementById('veiculo-marca').value = veiculo.marca;
        document.getElementById('veiculo-modelo').value = veiculo.modelo;
        document.getElementById('veiculo-ano').value = veiculo.ano || '';
        document.getElementById('veiculo-placa').value = veiculo.placa;
        document.getElementById('veiculo-cliente').value = veiculo.cliente_id;
    } catch (error) {
        console.error('Erro ao carregar veículo:', error);
    }
}

async function saveVeiculo() {
    const id = document.getElementById('veiculo-id').value;
    const marca = document.getElementById('veiculo-marca').value;
    const modelo = document.getElementById('veiculo-modelo').value;
    const ano = document.getElementById('veiculo-ano').value;
    const placa = document.getElementById('veiculo-placa').value;
    const cliente_id = document.getElementById('veiculo-cliente').value;
    
    if (!marca || !modelo || !placa || !cliente_id) {
        alert('Todos os campos obrigatórios devem ser preenchidos');
        return;
    }
    
    const data = { marca, modelo, ano, placa, cliente_id };
    
    try {
        let response;
        if (id) {
            data.id = id;
            response = await fetch(`${API_BASE}/veiculos.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            response = await fetch(`${API_BASE}/veiculos.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        
        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('veiculoModal')).hide();
            loadVeiculos();
            showAlert('Veículo salvo com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar veículo', 'danger');
        }
    } catch (error) {
        console.error('Erro ao salvar veículo:', error);
        showAlert('Erro ao salvar veículo', 'danger');
    }
}

function editVeiculo(id) {
    showVeiculoModal(id);
}

async function deleteVeiculo(id) {
    if (!confirm('Tem certeza que deseja excluir este veículo?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/veiculos.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        if (response.ok) {
            loadVeiculos();
            showAlert('Veículo excluído com sucesso!', 'success');
        } else {
            showAlert('Erro ao excluir veículo', 'danger');
        }
    } catch (error) {
        console.error('Erro ao excluir veículo:', error);
        showAlert('Erro ao excluir veículo', 'danger');
    }
}

// Orçamentos
async function loadOrcamentos() {
    try {
        const response = await fetch(`${API_BASE}/orcamentos.php`);
        const data = await response.json();
        
        allOrcamentos = data.records || data || [];
        displayOrcamentos(allOrcamentos);
    } catch (error) {
        console.error('Erro ao carregar orçamentos:', error);
    }
}

async function showOrcamentoModal(id = null) {
    document.getElementById('orcamentoForm').reset();
    document.getElementById('orcamento-id').value = '';
    currentOrcamentoId = id;
    itensOrcamento = [];
    
    // Resetar estado do modal
    const modal = document.getElementById('orcamentoModal');
    const inputs = modal.querySelectorAll('input, select');
    inputs.forEach(input => input.disabled = false);
    
    // Mostrar botão de adicionar item
    const addButton = modal.querySelector('button[onclick="addItem()"]');
    if (addButton) addButton.style.display = 'block';
    
    // Restaurar título do modal
    modal.querySelector('.modal-title').textContent = 'Orçamento';
    
    // Mostrar botão salvar
    const saveButton = modal.querySelector('button[onclick="saveOrcamento()"]');
    if (saveButton) saveButton.style.display = 'block';
    
    // Carregar veículos para o select
    await loadVeiculosSelect();
    
    if (id) {
        await loadOrcamentoData(id);
    }
    
    updateItensTable();
    new bootstrap.Modal(document.getElementById('orcamentoModal')).show();
}

async function loadVeiculosSelect() {
    try {
        const response = await fetch(`${API_BASE}/veiculos.php`);
        const data = await response.json();
        
        const select = document.getElementById('orcamento-veiculo');
        select.innerHTML = '<option value="">Selecione um veículo</option>';
        
        if (data.records && data.records.length > 0) {
            data.records.forEach(veiculo => {
                const option = document.createElement('option');
                option.value = veiculo.id;
                option.textContent = `${veiculo.marca} ${veiculo.modelo} - ${veiculo.placa} (${veiculo.cliente_nome})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar veículos:', error);
    }
}

async function loadOrcamentoData(id) {
    try {
        const [orcamento, itens] = await Promise.all([
            fetch(`${API_BASE}/orcamentos.php?id=${id}`).then(r => r.json()),
            fetch(`${API_BASE}/itens_orcamento.php?orcamento_id=${id}`).then(r => r.json())
        ]);
        
        document.getElementById('orcamento-id').value = orcamento.id;
        document.getElementById('orcamento-veiculo').value = orcamento.veiculo_id;
        document.getElementById('orcamento-status').value = orcamento.status;
        
        itensOrcamento = itens.records || [];
        updateItensTable();
    } catch (error) {
        console.error('Erro ao carregar orçamento:', error);
    }
}

function addItem() {
    const descricao = document.getElementById('item-descricao').value;
    const quantidade = parseInt(document.getElementById('item-quantidade').value);
    const valor = parseFloat(document.getElementById('item-valor').value);
    
    if (!descricao || !quantidade || !valor) {
        alert('Todos os campos do item devem ser preenchidos');
        return;
    }
    
    itensOrcamento.push({
        id: Date.now(), // ID temporário
        descricao,
        quantidade,
        valor_unitario: valor
    });
    
    // Limpar campos
    document.getElementById('item-descricao').value = '';
    document.getElementById('item-quantidade').value = '';
    document.getElementById('item-valor').value = '';
    
    updateItensTable();
}

function removeItem(index) {
    itensOrcamento.splice(index, 1);
    updateItensTable();
}

function updateItensTable() {
    const tbody = document.getElementById('itens-table');
    tbody.innerHTML = '';
    
    let total = 0;
    
    itensOrcamento.forEach((item, index) => {
        const subtotal = item.quantidade * item.valor_unitario;
        total += subtotal;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.descricao}</td>
            <td>${item.quantidade}</td>
            <td>R$ ${item.valor_unitario.toFixed(2)}</td>
            <td>R$ ${subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    document.getElementById('orcamento-total').textContent = `R$ ${total.toFixed(2)}`;
}

async function saveOrcamento() {
    const id = document.getElementById('orcamento-id').value;
    const veiculo_id = document.getElementById('orcamento-veiculo').value;
    const status = document.getElementById('orcamento-status').value;
    
    if (!veiculo_id) {
        alert('Veículo é obrigatório');
        return;
    }
    
    const total = itensOrcamento.reduce((sum, item) => sum + (item.quantidade * item.valor_unitario), 0);
    const data = { veiculo_id, status, valor_total: total };
    
    try {
        let response;
        let orcamentoId = id;
        
        if (id) {
            data.id = id;
            response = await fetch(`${API_BASE}/orcamentos.php`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            response = await fetch(`${API_BASE}/orcamentos.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            if (response.ok) {
                const result = await response.json();
                orcamentoId = result.id;
            }
        }
        
        if (response.ok && orcamentoId) {
            // Salvar itens
            await saveItensOrcamento(orcamentoId);
            
            bootstrap.Modal.getInstance(document.getElementById('orcamentoModal')).hide();
            loadOrcamentos();
            showAlert('Orçamento salvo com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar orçamento', 'danger');
        }
    } catch (error) {
        console.error('Erro ao salvar orçamento:', error);
        showAlert('Erro ao salvar orçamento', 'danger');
    }
}

async function saveItensOrcamento(orcamentoId) {
    // Se estamos editando, primeiro deletar itens existentes
    if (currentOrcamentoId) {
        const existingItens = await fetch(`${API_BASE}/itens_orcamento.php?orcamento_id=${orcamentoId}`).then(r => r.json());
        
        for (const item of existingItens.records || []) {
            await fetch(`${API_BASE}/itens_orcamento.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: item.id })
            });
        }
    }
    
    // Salvar novos itens
    for (const item of itensOrcamento) {
        await fetch(`${API_BASE}/itens_orcamento.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                orcamento_id: orcamentoId,
                descricao: item.descricao,
                quantidade: item.quantidade,
                valor_unitario: item.valor_unitario
            })
        });
    }
}

function viewOrcamento(id) {
    showOrcamentoModal(id);
    // Desabilitar edição no modo visualização
    setTimeout(() => {
        const modal = document.getElementById('orcamentoModal');
        const inputs = modal.querySelectorAll('input, select');
        inputs.forEach(input => input.disabled = true);
        
        // Esconder botão de adicionar item
        const addButton = modal.querySelector('button[onclick="addItem()"]');
        if (addButton) addButton.style.display = 'none';
        
        // Esconder botões de remover item
        const removeButtons = modal.querySelectorAll('button[onclick^="removeItem"]');
        removeButtons.forEach(btn => btn.style.display = 'none');
        
        // Alterar título do modal
        modal.querySelector('.modal-title').textContent = 'Visualizar Orçamento';
        
        // Esconder botão salvar
        const saveButton = modal.querySelector('button[onclick="saveOrcamento()"]');
        if (saveButton) saveButton.style.display = 'none';
    }, 100);
}

function editOrcamento(id) {
    showOrcamentoModal(id);
}

async function deleteOrcamento(id) {
    if (!confirm('Tem certeza que deseja excluir este orçamento?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/orcamentos.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        if (response.ok) {
            loadOrcamentos();
            showAlert('Orçamento excluído com sucesso!', 'success');
        } else {
            showAlert('Erro ao excluir orçamento', 'danger');
        }
    } catch (error) {
        console.error('Erro ao excluir orçamento:', error);
        showAlert('Erro ao excluir orçamento', 'danger');
    }
}

// Funções auxiliares
function getStatusColor(status) {
    switch(status) {
        case 'Pendente': return 'warning';
        case 'Aprovado': return 'success';
        case 'Rejeitado': return 'danger';
        case 'Concluído': return 'primary';
        default: return 'secondary';
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}



// Configurações da Oficina
async function loadConfiguracoes() {
    try {
        const response = await fetch(`${API_BASE}/configuracoes.php`);
        const data = await response.json();
        
        document.getElementById('config-nome-oficina').value = data.nome_oficina || '';
        document.getElementById('config-cnpj').value = data.cnpj || '';
        document.getElementById('config-endereco').value = data.endereco || '';
        document.getElementById('config-telefone1').value = data.telefone1 || '';
        document.getElementById('config-telefone2').value = data.telefone2 || '';
        document.getElementById('config-email').value = data.email_contato || '';
        document.getElementById('config-logo').value = data.logo_url || '';
    } catch (error) {
        console.error('Erro ao carregar configurações:', error);
    }
}

async function saveConfiguracoes() {
    const nome_oficina = document.getElementById('config-nome-oficina').value;
    const cnpj = document.getElementById('config-cnpj').value;
    const endereco = document.getElementById('config-endereco').value;
    const telefone1 = document.getElementById('config-telefone1').value;
    const telefone2 = document.getElementById('config-telefone2').value;
    const email_contato = document.getElementById('config-email').value;
    const logo_url = document.getElementById('config-logo').value;
    
    if (!nome_oficina) {
        alert('Nome da oficina é obrigatório');
        return;
    }
    
    const data = {
        nome_oficina,
        cnpj,
        endereco,
        telefone1,
        telefone2,
        email_contato,
        logo_url
    };
    
    try {
        const response = await fetch(`${API_BASE}/configuracoes.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações', 'danger');
        }
    } catch (error) {
        console.error('Erro ao salvar configurações:', error);
        showAlert('Erro ao salvar configurações', 'danger');
    }
}



// Função de impressão de orçamentos
async function imprimirOrcamento(id) {
    try {
        const response = await fetch(`${API_BASE}/imprimir_orcamento.php?id=${id}`);
        const data = await response.json();
        
        if (response.ok) {
            // Criar janela de impressão
            const printWindow = window.open('', '_blank');
            const printContent = generatePrintHTML(data);
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        } else {
            showAlert('Erro ao carregar dados para impressão', 'danger');
        }
    } catch (error) {
        console.error('Erro ao imprimir orçamento:', error);
        showAlert('Erro ao imprimir orçamento', 'danger');
    }
}

function generatePrintHTML(data) {
    const { orcamento, itens, configuracao } = data;
    const dataFormatada = new Date(orcamento.data_criacao).toLocaleDateString('pt-BR');
    
    let itensHTML = '';
    let total = 0;
    
    itens.forEach(item => {
        const subtotal = parseFloat(item.quantidade) * parseFloat(item.valor_unitario);
        total += subtotal;
        itensHTML += `
            <tr>
                <td>${item.descricao}</td>
                <td style="text-align: center;">${item.quantidade}</td>
                <td style="text-align: right;">R$ ${parseFloat(item.valor_unitario).toFixed(2)}</td>
                <td style="text-align: right;">R$ ${subtotal.toFixed(2)}</td>
            </tr>
        `;
    });
    
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Orçamento #${orcamento.id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .logo { max-height: 80px; margin-bottom: 10px; }
                .company-info { margin-bottom: 20px; }
                .orcamento-info { margin-bottom: 30px; }
                .cliente-info { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
                .info-section { margin-bottom: 20px; }
                .info-title { font-weight: bold; margin-bottom: 10px; color: #333; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                ${configuracao.logo_url ? `<img src="${configuracao.logo_url}" alt="Logo" class="logo">` : ''}
                <h1>${configuracao.nome_oficina || 'Oficina Mecânica'}</h1>
                <div class="company-info">
                    ${configuracao.endereco ? `<p><strong>Endereço:</strong> ${configuracao.endereco}</p>` : ''}
                    <p>
                        ${configuracao.telefone1 ? `<strong>Telefone:</strong> ${configuracao.telefone1}` : ''}
                        ${configuracao.telefone2 ? ` / ${configuracao.telefone2}` : ''}
                    </p>
                    ${configuracao.email_contato ? `<p><strong>Email:</strong> ${configuracao.email_contato}</p>` : ''}
                    ${configuracao.cnpj ? `<p><strong>CNPJ:</strong> ${configuracao.cnpj}</p>` : ''}
                </div>
            </div>
            
            <div class="orcamento-info">
                <div class="info-title">ORÇAMENTO #${orcamento.id}</div>
                <p><strong>Data:</strong> ${dataFormatada}</p>
                <p><strong>Status:</strong> ${orcamento.status}</p>
            </div>
            
            <div class="cliente-info">
                <div class="info-title">DADOS DO CLIENTE</div>
                <p><strong>Nome:</strong> ${orcamento.cliente_nome}</p>
                ${orcamento.telefone ? `<p><strong>Telefone:</strong> ${orcamento.telefone}</p>` : ''}
                ${orcamento.email ? `<p><strong>Email:</strong> ${orcamento.email}</p>` : ''}
            </div>
            
            <div class="info-section">
                <div class="info-title">DADOS DO VEÍCULO</div>
                <p><strong>Veículo:</strong> ${orcamento.marca} ${orcamento.modelo} (${orcamento.ano})</p>
                <p><strong>Placa:</strong> ${orcamento.placa}</p>
            </div>
            
            <div class="info-section">
                <div class="info-title">ITENS DO ORÇAMENTO</div>
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th style="width: 80px;">Qtd</th>
                            <th style="width: 120px;">Valor Unit.</th>
                            <th style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itensHTML}
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>TOTAL GERAL:</strong></td>
                            <td style="text-align: right;"><strong>R$ ${total.toFixed(2)}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
                <p>Este orçamento tem validade de 30 dias a partir da data de emissão.</p>
            </div>
        </body>
        </html>
    `;
}


// Variáveis globais para armazenar dados originais
let allClientes = [];
let allVeiculos = [];
let allOrcamentos = [];

// Funções de filtro para Clientes
function filterClientes() {
    const searchTerm = document.getElementById('search-clientes').value.toLowerCase();
    const filterField = document.getElementById('filter-clientes').value;
    
    let filteredClientes = allClientes;
    
    if (searchTerm) {
        filteredClientes = allClientes.filter(cliente => {
            if (filterField) {
                // Filtrar por campo específico
                const fieldValue = cliente[filterField] || '';
                return fieldValue.toLowerCase().includes(searchTerm);
            } else {
                // Filtrar por todos os campos
                return (
                    (cliente.nome || '').toLowerCase().includes(searchTerm) ||
                    (cliente.telefone || '').toLowerCase().includes(searchTerm) ||
                    (cliente.email || '').toLowerCase().includes(searchTerm)
                );
            }
        });
    }
    
    displayClientes(filteredClientes);
}

function displayClientes(clientes) {
    const tbody = document.getElementById('clientes-table');
    tbody.innerHTML = '';
    
    if (clientes.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="4" class="text-center">Nenhum cliente encontrado</td>';
        tbody.appendChild(row);
        return;
    }
    
    clientes.forEach(cliente => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${cliente.id}</td>
            <td>${cliente.nome}</td>
            <td>${cliente.telefone || 'N/A'}</td>
            <td>${cliente.email || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editCliente(${cliente.id})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteCliente(${cliente.id})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Funções de filtro para Veículos
function filterVeiculos() {
    const searchTerm = document.getElementById('search-veiculos').value.toLowerCase();
    const filterField = document.getElementById('filter-veiculos').value;
    
    let filteredVeiculos = allVeiculos;
    
    if (searchTerm) {
        filteredVeiculos = allVeiculos.filter(veiculo => {
            if (filterField) {
                // Filtrar por campo específico
                if (filterField === 'cliente') {
                    return (veiculo.cliente_nome || '').toLowerCase().includes(searchTerm);
                } else {
                    const fieldValue = veiculo[filterField] || '';
                    return fieldValue.toLowerCase().includes(searchTerm);
                }
            } else {
                // Filtrar por todos os campos
                return (
                    (veiculo.marca || '').toLowerCase().includes(searchTerm) ||
                    (veiculo.modelo || '').toLowerCase().includes(searchTerm) ||
                    (veiculo.placa || '').toLowerCase().includes(searchTerm) ||
                    (veiculo.cliente_nome || '').toLowerCase().includes(searchTerm)
                );
            }
        });
    }
    
    displayVeiculos(filteredVeiculos);
}

function displayVeiculos(veiculos) {
    const tbody = document.getElementById('veiculos-table');
    tbody.innerHTML = '';
    
    if (veiculos.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="6" class="text-center">Nenhum veículo encontrado</td>';
        tbody.appendChild(row);
        return;
    }
    
    veiculos.forEach(veiculo => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${veiculo.id}</td>
            <td>${veiculo.marca}</td>
            <td>${veiculo.modelo}</td>
            <td>${veiculo.ano || 'N/A'}</td>
            <td>${veiculo.placa}</td>
            <td>${veiculo.cliente_nome || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editVeiculo(${veiculo.id})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteVeiculo(${veiculo.id})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Funções de filtro para Orçamentos
function filterOrcamentos() {
    const searchTerm = document.getElementById('search-orcamentos').value.toLowerCase();
    const filterField = document.getElementById('filter-orcamentos').value;
    const statusFilter = document.getElementById('filter-status-orcamentos').value;
    
    let filteredOrcamentos = allOrcamentos;
    
    // Filtrar por status primeiro
    if (statusFilter) {
        filteredOrcamentos = filteredOrcamentos.filter(orcamento => 
            orcamento.status === statusFilter
        );
    }
    
    // Filtrar por termo de pesquisa
    if (searchTerm) {
        filteredOrcamentos = filteredOrcamentos.filter(orcamento => {
            if (filterField) {
                // Filtrar por campo específico
                if (filterField === 'cliente') {
                    return (orcamento.cliente_nome || '').toLowerCase().includes(searchTerm);
                } else if (filterField === 'veiculo') {
                    const veiculoInfo = `${orcamento.marca} ${orcamento.modelo} ${orcamento.placa}`;
                    return veiculoInfo.toLowerCase().includes(searchTerm);
                } else if (filterField === 'status') {
                    return (orcamento.status || '').toLowerCase().includes(searchTerm);
                }
            } else {
                // Filtrar por todos os campos
                const veiculoInfo = `${orcamento.marca} ${orcamento.modelo} ${orcamento.placa}`;
                return (
                    (orcamento.cliente_nome || '').toLowerCase().includes(searchTerm) ||
                    veiculoInfo.toLowerCase().includes(searchTerm) ||
                    (orcamento.status || '').toLowerCase().includes(searchTerm)
                );
            }
        });
    }
    
    displayOrcamentos(filteredOrcamentos);
}

function displayOrcamentos(orcamentos) {
    const tbody = document.getElementById('orcamentos-table');
    tbody.innerHTML = '';
    
    if (orcamentos.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="7" class="text-center">Nenhum orçamento encontrado</td>';
        tbody.appendChild(row);
        return;
    }
    
    orcamentos.forEach(orcamento => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${orcamento.id}</td>
            <td>${orcamento.cliente_nome || 'N/A'}</td>
            <td>${orcamento.marca} ${orcamento.modelo} - ${orcamento.placa}</td>
            <td>${new Date(orcamento.data_criacao).toLocaleDateString('pt-BR')}</td>
            <td><span class="badge bg-${getStatusColor(orcamento.status)}">${orcamento.status}</span></td>
            <td>R$ ${parseFloat(orcamento.valor_total).toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewOrcamento(${orcamento.id})" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editOrcamento(${orcamento.id})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info me-1" onclick="imprimirOrcamento(${orcamento.id})" title="Imprimir">
                    <i class="fas fa-print"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteOrcamento(${orcamento.id})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

