<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/jquery/jquery.min.js"></script>
    <style>
        nav div { max-height: 40px; }
        ol.breadcrumb { 
            & li, a, li::before { color: white !important; }
        }
        .hiden { display: none; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="d-flex justify-content-between px-4 w-100">
            <span class="navbar-brand">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                    </ol>
                </nav>
            </span>
            <button onclick="logout()" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Sair</button>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form id="perfil">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label for="data-nascimento" class="form-label">Data de nascimento</label>
                                    <input type="date" class="form-control" id="data-nascimento" name="data-nascimento" required>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label for="data-cadastro" class="form-label">Membro desde</label>
                                    <input type="month" class="form-control" id="data-cadastro" disabled>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3 text-secondary">Alterar Senha <small class="fs-6 fw-normal">(Opcional)</small></h5>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Deixe em branco para manter a atual">
                                <div class="form-text small mt-2">
                                    <button type="button" class="btn btn-link p-0 text-decoration-none text-danger" onclick="$('#requisitos-senha').toggleClass('hiden')" tabindex="-1">
                                        <i class="bi bi-shield-lock"></i> Requisitos
                                    </button>
                                    <ul id="requisitos-senha" class="hiden">
                                        <li>8 caracteres</li>
                                        <li>1 letra maiúscula</li>
                                        <li>1 letra minúscula</li> 
                                        <li>1 número</li>
                                        <li>1 símbolo</li>
                                    </ul>
                                </div>
                            </div>

                            <input type="hidden" id="id" name="id">

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary" id="btnSave">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="mensagem-aviso"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');

        function emiteAviso(message, type = 'danger') {
            let div = $('#liveToast');
            let body = $('#mensagem-aviso');

            div.attr('class', `toast align-items-center text-white bg-${type} border-0`);
            body.text(message);
            
            let toast = bootstrap.Toast.getOrCreateInstance(div);
            toast.show();
        }

        async function carregaPerfil() {
            try {
                const res = await fetch('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (res.ok) {
                    const user = await res.json();
                    let form = $('#perfil');
                    form.find('#id').val(user.id);
                    form.find('#nome').val(user.nome);
                    form.find('#email').val(user.email);
                    form.find('#data-nascimento').val(user.dataNascimento.date.substring(0, 10));
                    form.find('#data-cadastro').val(user.dataCadastro.date.substring(0, 7));
                } else {
                    logout();
                }
            } catch (error) {
                console.error(error);    
                emiteAviso('Erro ao carregar suas informações');
            }
        }

        $('form').on('submit', function (event) {
            event.preventDefault();
            let form = $(this);
            let dataForm = new FormData(form[0]);

            let btn = form.find('button:submit');
            let btnOriginal = btn.html();
            btn.prop('disabled', true).html('Processando...');

            $.ajax({
                url: '/api/user/update',
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                dataType: 'json',
                data: JSON.stringify(
                    Object.fromEntries(dataForm.entries())
                ),
                success: function(retorno) { 
                    if(retorno.ok){
                        emiteAviso('Informações atualizadas!', 'success');
                    } else {
                        emiteAviso(retorno.error, 'info');
                    }
                },
                error: function() { emiteAviso('Não foi possível atualizar suas informações. Tente novamente mais tarde', 'danger') },
                complete: function() { 
                    btn.prop('disabled', false).html(btnOriginal);
                }
            });
        })

        function logout() {
            $.ajax({
                url: '/api/logout',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                complete: function() { 
                    location.reload();
                }
            });
        }

        $(fn => {
            carregaPerfil();
        });
    </script>
</body>
</html>