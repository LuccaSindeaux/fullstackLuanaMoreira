 function verFichas(clienteId) {
            alert(`Funcionalidade "Ver Fichas" para o cliente ${clienteId} a ser implementada.`);
        }

        document.addEventListener('DOMContentLoaded', function () {
            fetch(`${API_URL}/php/verifica_login.php`)
                .then(res => res.json())
                .then(data => {
                    if (!data.logado || !data.admin) {
                        alert('Acesso negado. Esta é uma área administrativa.');
                        window.location.href = '../index.html';
                    }
                })
                .catch(() => {
                    alert('Erro de autenticação. Redirecionando para a página inicial.');
                    window.location.href = '../index.html';
                });

            // LÓGICA PARA EDITAR PERFIL
            const formPerfil = document.getElementById('form-perfil');
            // Carrega os dados atuais do admin no formulário
            fetch(`${API_URL}/php/buscar_dados_usuario.php`)
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        formPerfil.nome.value = data.usuario.nome;
                        formPerfil.email.value = data.usuario.email;
                    }
                });

            formPerfil.addEventListener('submit', function (e) {
                e.preventDefault();
                const dados = {
                    nome: this.nome.value,
                    email: this.email.value,
                    senha: this.senha.value
                };
                fetch(`${API_URL}/php/atualizar_perfil.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(dados)
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.mensagem);
                    if(data.sucesso) this.senha.value = ''; // Limpa o campo de senha
                });
            });

            // LÓGICA PARA LISTAR CLIENTES 
            const listaClientesEl = document.getElementById('lista-clientes');
            fetch(`${API_URL}/php/listar_fichas.php`)
                .then(res => res.json())
                .then(clientes => {
                    listaClientesEl.innerHTML = ''; 
                    if (clientes.length === 0) {
                        listaClientesEl.innerHTML = '<p>Nenhum cliente cadastrado ainda.</p>';
                        return;
                    }
                    clientes.forEach(cliente => {
                        const clienteDiv = document.createElement('div');
                        clienteDiv.className = 'ficha-cliente';
                        clienteDiv.innerHTML = `
                            <h4>${cliente.nome}</h4>
                            <p><strong>E-mail:</strong> ${cliente.email}</p>
                            <p><strong>Telefone:</strong> ${cliente.telefone || 'Não informado'}</p>
                            <button onclick="verFichas(${cliente.id})">Ver Fichas de Anamnese</button>
                        `;
                        listaClientesEl.appendChild(clienteDiv);
                    });
                });

            // LÓGICA DO CALENDÁRIO DE DISPONIBILIDADE 
            const calendarEl = document.getElementById('calendario-disponibilidade');
            const modalEditar = document.getElementById('modalEditar');
            const modalExcluir = document.getElementById('modalExcluir');
            let eventoSelecionado = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: "08:00:00",
                slotMaxTime: "22:00:00",
                allDaySlot: false,
                selectable: true,
                events: {
                    url: `${API_URL}/php/disponibilidade.php`,
                },
                eventDataTransform: function(eventInfo) {
                    if (eventInfo.extendedProps.status === 'indisponivel') {
                        eventInfo.backgroundColor = '#7f8c8d';
                        eventInfo.borderColor = '#7f8c8d';
                        eventInfo.title = 'Indisponível';
                    } else {
                        eventInfo.backgroundColor = '#27ae60';
                        eventInfo.borderColor = '#27ae60';
                        eventInfo.title = 'Disponível';
                    }
                    return eventInfo;
                },
                select: function(info) {
                    const dataHora = info.startStr.slice(0, 19).replace('T', ' ');
                    if (confirm(`Deseja criar um novo horário disponível em ${new Date(dataHora).toLocaleString('pt-BR')}?`)) {
                        fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({ data_hora: dataHora, status: 'disponivel' })
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.mensagem);
                            calendar.refetchEvents();
                        });
                    }
                },
                eventClick: function(info) {
                    eventoSelecionado = info.event;
                    document.getElementById('infoEvento').textContent = `Data: ${eventoSelecionado.start.toLocaleString('pt-BR')}`;
                    document.getElementById('selectStatus').value = eventoSelecionado.extendedProps.status;
                    modalEditar.style.display = 'flex';
                }
            });

            calendar.render();

            // LÓGICA DOS MODAIS DE EDIÇÃO/EXCLUSÃO 
            document.getElementById('btnSalvarEdicao').addEventListener('click', () => {
                if (!eventoSelecionado) return;
                const novoStatus = document.getElementById('selectStatus').value;
                fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ id: eventoSelecionado.id, status: novoStatus })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.mensagem);
                    modalEditar.style.display = 'none';
                    calendar.refetchEvents();
                });
            });

            document.getElementById('btnConfirmarExcluir').addEventListener('click', () => {
                if (!eventoSelecionado) return;
                fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
                    method: 'DELETE',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ id: eventoSelecionado.id })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.mensagem);
                    modalExcluir.style.display = 'none';
                    calendar.refetchEvents();
                });
            });

            document.getElementById('btnCancelarEdicao').addEventListener('click', () => modalEditar.style.display = 'none');
            document.getElementById('btnCancelarExcluir').addEventListener('click', () => modalExcluir.style.display = 'none');
            document.getElementById('btnAbrirModalExcluir').addEventListener('click', () => {
                modalEditar.style.display = 'none';
                modalExcluir.style.display = 'flex';
            });
        });