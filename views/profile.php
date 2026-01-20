<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">Editar Perfil</span>
            <div class="d-flex gap-2">
                <a href="/home" class="btn btn-sm btn-outline-light">Voltar para Home</a>
                <button onclick="logout()" class="btn btn-sm btn-danger">Sair</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3 text-primary">Seus Dados</h4>
                        
                        <div id="alertBox" class="alert d-none"></div>

                        <form id="profileForm">
                            <div class="mb-3">
                                <label class="form-label text-muted">E-mail (Identificação)</label>
                                <input type="email" id="email" class="form-control bg-light" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" id="nome" class="form-control" required>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3 text-secondary">Alterar Senha <small class="fs-6 fw-normal">(Opcional)</small></h5>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova Senha</label>
                                <input type="password" id="senha" class="form-control" placeholder="Deixe em branco para manter a atual">
                                <div class="form-text small mt-1">
                                    Requisitos: 8 caracteres, Maiúscula, Minúscula, Número e Símbolo.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary" id="btnSave">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

        const alertBox = document.getElementById('alertBox');
        const nameInput = document.getElementById('nome');
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('senha');

        async function loadProfile() {
            try {
                const res = await fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (res.ok) {
                    const user = await res.json();
                    nameInput.value = user.nome;
                    emailInput.value = user.email;
                } else {
                    logout();
                }
            } catch (error) {
                showAlert('Erro ao carregar dados.', 'danger');
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSave');
            btn.disabled = true;
            btn.textContent = 'Salvando...';
            
            const payload = {
                name: nameInput.value
            };

            if (passInput.value.trim() !== "") {
                payload.password = passInput.value;
            }

            try {
                const res = await fetch('/api/user', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok) {
                    showAlert('Perfil atualizado com sucesso!', 'success');
                    passInput.value = '';
                } else {
                    showAlert(data.error || 'Erro ao atualizar.', 'danger');
                }
            } catch (error) {
                showAlert('Erro de conexão.', 'danger');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Salvar Alterações';
            }
        });

        function logout() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }

        function showAlert(msg, type) {
            alertBox.className = `alert alert-${type}`;
            alertBox.textContent = msg;
            alertBox.classList.remove('d-none');
            setTimeout(() => alertBox.classList.add('d-none'), 3000);
        }

        loadProfile();
    </script>
</body>
</html>