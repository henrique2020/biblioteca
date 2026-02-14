<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php require_once view_path('layout/head.php'); ?>
    <title>Biblioteca Central</title>
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 90vw; min-width: 400px; border-radius: 15px; border: none; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container d-flex justify-content-center">
        <div class="card login-card shadow-lg p-4">
            <div class="card-body">
                <form id="acesso" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="/cadastre-se" class="btn btn-secondary btn-sm flex-grow-1 p-1">Cadastre-se</a>
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
        function emiteAviso(message, type = 'danger') {
            let div = $('#liveToast');
            let body = $('#mensagem-aviso');

            div.attr('class', `toast align-items-center text-white bg-${type} border-0`);
            body.text(message);
            
            let toast = bootstrap.Toast.getOrCreateInstance(div);
            toast.show();
        }

        $('form').on('submit', function(event) {
            event.preventDefault();
            let form = $(this);
            let dataForm = new FormData(form[0]);
            let vazios = form.find('input').filter(function() {
                return $.trim($(this).val()) === "";
            });
            if(vazios.length > 0) { return; }

            let btn = form.find('button:submit');
            let btnOriginal = btn.html();
            btn.prop('disabled', true).html('Processando...');
            $.ajax({
                url: '/api/login',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(
                    Object.fromEntries(dataForm.entries())
                ),
                success: function(retorno) { 
                    if(retorno.ok){
                        localStorage.setItem('token', retorno.token);
                        window.location.href = retorno.redirect;
                    } else {
                        emiteAviso(retorno.error, 'info');
                    }
                },
                error: function() { emiteAviso('Não foi possível efetuar seu login. Tente novamente mais tarde', 'danger') },
                complete: function() { 
                    btn.prop('disabled', false).html(btnOriginal);
                }
            });
        });

        $(fn => {
            $('#email').focus();
        });
    </script>
</body>
</html>
