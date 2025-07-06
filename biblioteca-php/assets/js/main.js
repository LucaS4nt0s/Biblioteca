// Funções utilitárias
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.innerHTML = message;
    
    const container = document.querySelector('.main-content .container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remover após 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Validação de CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    resto = 11 - (soma % 11);
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Formatação de CPF
function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
}

// Formatação de telefone
function formatarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
    telefone = telefone.replace(/(\d{4})(\d)/, '$1-$2');
    telefone = telefone.replace(/(\d{4})-(\d)(\d{4})/, '$1$2-$3');
    return telefone;
}

// Confirmação de ações
function confirmarAcao(mensagem, callback) {
    if (confirm(mensagem)) {
        callback();
    }
}

// Busca em tempo real
function setupLiveSearch(inputId, tableId, columns = []) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            let found = false;
            const cells = rows[i].getElementsByTagName('td');
            
            if (columns.length === 0) {
                // Buscar em todas as colunas
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
            } else {
                // Buscar apenas nas colunas especificadas
                for (let col of columns) {
                    if (cells[col] && cells[col].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    });
}

// Paginação simples
function setupPagination(tableId, rowsPerPage = 10) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = Array.from(table.getElementsByTagName('tr')).slice(1); // Excluir header
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    let currentPage = 1;
    
    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });
        
        updatePaginationControls();
    }
    
    function updatePaginationControls() {
        let paginationDiv = document.getElementById('pagination-' + tableId);
        if (!paginationDiv) {
            paginationDiv = document.createElement('div');
            paginationDiv.id = 'pagination-' + tableId;
            paginationDiv.className = 'pagination-controls text-center mt-3';
            table.parentNode.appendChild(paginationDiv);
        }
        
        let html = '';
        
        // Botão anterior
        if (currentPage > 1) {
            html += `<button class="btn btn-secondary" onclick="changePage(${currentPage - 1})">Anterior</button> `;
        }
        
        // Números das páginas
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'btn-primary' : 'btn-secondary';
            html += `<button class="btn ${activeClass}" onclick="changePage(${i})">${i}</button> `;
        }
        
        // Botão próximo
        if (currentPage < totalPages) {
            html += `<button class="btn btn-secondary" onclick="changePage(${currentPage + 1})">Próximo</button>`;
        }
        
        paginationDiv.innerHTML = html;
    }
    
    window.changePage = function(page) {
        currentPage = page;
        showPage(page);
    };
    
    showPage(1);
}

// Modal simples
function showModal(title, content, buttons = []) {
    // Remover modal existente
    const existingModal = document.getElementById('custom-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modal = document.createElement('div');
    modal.id = 'custom-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        border-radius: 15px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    `;
    
    let buttonsHtml = '';
    if (buttons.length === 0) {
        buttonsHtml = '<button class="btn btn-secondary" onclick="closeModal()">Fechar</button>';
    } else {
        buttonsHtml = buttons.map(btn => 
            `<button class="btn ${btn.class || 'btn-secondary'}" onclick="${btn.onclick || 'closeModal()'}">${btn.text}</button>`
        ).join(' ');
    }
    
    modalContent.innerHTML = `
        <h3>${title}</h3>
        <div class="mt-3">${content}</div>
        <div class="mt-4 text-center">
            ${buttonsHtml}
        </div>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Fechar ao clicar fora
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
}

function closeModal() {
    const modal = document.getElementById('custom-modal');
    if (modal) {
        modal.remove();
    }
}

// Inicialização quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar formatação automática em campos CPF
    const cpfInputs = document.querySelectorAll('input[name="cpf"], input[id*="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatarCPF(this.value);
        });
    });
    
    // Aplicar formatação automática em campos telefone
    const telefoneInputs = document.querySelectorAll('input[name="telefone"], input[id*="telefone"]');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatarTelefone(this.value);
        });
    });
    
    // Validação de formulários
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const cpfInput = form.querySelector('input[name="cpf"]');
            if (cpfInput && cpfInput.value) {
                const cpfLimpo = cpfInput.value.replace(/\D/g, '');
                if (!validarCPF(cpfLimpo)) {
                    e.preventDefault();
                    showAlert('CPF inválido!', 'error');
                    cpfInput.focus();
                    return false;
                }
            }
        });
    });
    
    // Configurar busca em tempo real se existir
    if (document.getElementById('search-input') && document.getElementById('data-table')) {
        setupLiveSearch('search-input', 'data-table');
    }
    
    // Configurar paginação se existir
    if (document.getElementById('data-table')) {
        setupPagination('data-table');
    }
    
    // Mostrar alertas do PHP se existirem
    const urlParams = new URLSearchParams(window.location.search);
    const sucesso = urlParams.get('sucesso');
    const erro = urlParams.get('erro');
    
    if (sucesso) {
        showAlert(decodeURIComponent(sucesso), 'success');
    }
    
    if (erro) {
        showAlert(decodeURIComponent(erro), 'error');
    }
});

// Função para recarregar página sem parâmetros
function limparUrl() {
    const url = window.location.href.split('?')[0];
    window.history.replaceState({}, document.title, url);
}

