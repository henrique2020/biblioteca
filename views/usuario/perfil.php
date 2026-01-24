<?php
$lista_navegacao = [
    ['nome' => 'Home', 'link' => '/'],
    ['nome' => 'Perfil', 'link' => '']
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php require_once view_path('layout/head.php'); ?>
    <title>Meu Perfil</title>
    <style>
        .hiden { display: none; }
    </style>
</head>
<body class="bg-light">
    <?php require_once view_path('layout/nav.php'); ?>

    <main class="container">
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
    </main>

    <?php require_once view_path('layout/footer.php'); ?>

    <script>
        const token = localStorage.getItem('token');

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

        $(fn => {
            carregaPerfil();
        });
    </script>
</body>
</html>