<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php require_once view_path('layout/head.php'); ?>
    <title>Novo Livro</title>
    
    <link href="/assets/tagify/tagify.css" rel="stylesheet" />
    <script src="/assets/tagify/tagify.min.js"></script>
    
    <style>
        body { background-color: #f8f9fa; }
        .card { max-width: 90vw; border-radius: 15px; border: none; }
        /* Ajuste opcional para o input parecer mais com o Bootstrap */
        .tagify { --tags-border-color: #dee2e6; --tags-focus-border-color: #86b7fe; }
    </style>
</head>
<body>
    <?php require_once view_path('layout/nav.php'); ?>

    <main class="container mt-5">
        <div class="card shadow-lg p-4">
            <div class="card-body">
                <form id="cadastro">
                    <div class="mb-3">
                        <label for="livro" class="form-label">Título</label>
                        <input type="text" class="form-control" id="livro" name="livro" required>
                    </div>
                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor</label>
                        <input type="text" class="form-control" id="autor" name="autor" required>
                    </div>
                    <div class="mb-3">
                        <label for="data-lancamento" class="form-label">Data de lançamento</label>
                        <input type="date" class="form-control" id="data-lancamento" name="data-lancamento" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="generos" class="form-label">Gêneros</label>
                        <input class="form-control" id="generos" name="generos" placeholder="Digite para buscar...">
                        <div class="form-text">Digite e pressione Enter ou selecione da lista.</div>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg">Cadastrar livro</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once view_path('layout/footer.php'); ?>

    <script>
        // Função auxiliar para remover acentos e normalizar texto
        function normalizaTexto(texto) {
            return texto ? texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase() : "";
        }
        
        var generos = [];
        const tagify = new Tagify($('#generos')[0], {
            whitelist: [], 
            enforceWhitelist: false,    // True: Apenas o que vier do banco | False: Aceita o que for digitado
            dropdown: {
                maxItems: 10,
                classname: "tags-look",
                enabled: 1,             // 0: Mostra sugestões ao focar | 1: Mostra sugestões apenas ao digitar
                closeOnSelect: true,
                highlightFirst: true
            },
            keepInvalidTags: false      // False: desativa a busca nativa
        });

        tagify.on('input', function(e) {
            var busca = e.detail.value;
            var normalizado = normalizaTexto(busca);

            // Se não digitou nada, reseta a lista
            if (!busca) {
                tagify.settings.whitelist = [];
                return;
            }
            
            tagify.settings.whitelist = generos.filter(function(item) {
                var palavras = item.searchBy.split(' ');

                return palavras.some(function(palavra) {
                    return palavra.startsWith(normalizado);
                });
            });

            tagify.dropdown.show(busca); // Força a atualização do dropdown
        });

        $.ajax({
            url: '/api/livro/generos',
            method: 'GET',
            success: function(data) {
                generos = data.map(item => ({
                    value: item.genero,
                    id: item.id,
                    searchBy: normalizaTexto(item.genero)
                }));
            }
        });

        $('form').on('submit', function(event) {
            event.preventDefault();
            let form = $(this);
            let btn = form.find('button:submit');
            let btnOriginal = btn.html();

            let vazios = form.find('input[required], textarea[required]').filter(function() {
                return $.trim($(this).val()) === "";
            });
            if(vazios.length > 0) { 
                vazios.first().focus();
                return; 
            }

            let dataForm = new FormData(form[0]);

            try {
                let generosSelecionados = (tagify.value).map(function(item) {
                    return {
                        id: item.id || null,  // Se tiver ID usa ele, senão envia null (é novo)
                        value: item.value     // O nome do gênero
                    };
                });
                dataForm.set('generos', JSON.stringify(generosSelecionados));
            } catch (e) {
                dataForm.set('generos', '[]');
            }

            btn.prop('disabled', true).html('Processando...');

            $.ajax({
                url: '/api/livro/create',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(Object.fromEntries(dataForm.entries())),
                success: function(retorno) { 
                    if(retorno.ok){
                        emiteAviso('Livro cadastrado com sucesso!', 'success');
                        setTimeout(() => { window.location.href = retorno.redirect || '/catalogo'; }, 2000);
                    } else {
                        emiteAviso(retorno.error, 'info');
                    }
                },
                error: function() { 
                    emiteAviso('Não foi possível cadastrar este livro. Tente novamente mais tarde', 'danger'); 
                },
                complete: function() { 
                    btn.prop('disabled', false).html(btnOriginal);
                }
            });
        });
    </script>
</body>
</html>