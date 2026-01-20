<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bootstrap Local</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 400px; width: 100%; border-radius: 15px; border: none; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

    <div class="container d-flex justify-content-center">
        <div class="card login-card shadow-lg p-4">
            <div class="card-body">
                <h3 class="text-center mb-4 fw-bold text-primary">Acesso Local</h3>
                <div id="alertBox" class="alert d-none" role="alert"></div>

                <form id="loginForm" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" required>
                        <div class="form-text small mt-2">
                            <i class="bi bi-shield-lock"></i> Requisitos: 12 chars, Maiúscula, Minúscula, Número, Símbolo.
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="button" id="btnRegister" class="btn btn-outline-success btn-sm flex-grow-1">Criar Conta de teste</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        const alertBox = document.getElementById('alertBox');
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('senha');

        function showAlert(message, type) {
            alertBox.className = `alert ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
            alertBox.textContent = message;
            alertBox.classList.remove('d-none');
            setTimeout(() => alertBox.classList.add('d-none'), 4000);
        }

        function markInputError(hasError) {
            const method = hasError ? 'add' : 'remove';
            emailInput.classList[method]('is-invalid');
            passInput.classList[method]('is-invalid');
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            markInputError(false);
            const email = emailInput.value;
            const password = passInput.value;

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                if (res.ok) {
                    localStorage.setItem('token', data.token);
                    window.location.href = '/home';
                } else {
                    showAlert(data.error, 'error');
                    markInputError(true);
                }
            } catch (error) { showAlert('Erro de conexão.', 'error'); }
        });

        document.getElementById('btnRegister').addEventListener('click', async () => {
            const email = emailInput.value;
            const password = passInput.value;
            if(!email || !password) { showAlert('Preencha os campos.', 'error'); return; }

            const res = await fetch('/api/register', {
                method: 'POST',
                body: JSON.stringify({ name: 'User Local', email, password })
            });
            const data = await res.json();
            if(res.ok) { showAlert(data.message, 'success'); markInputError(false); }
            else { showAlert(data.error, 'error'); }
        });
    </script>
</body>
</html>
