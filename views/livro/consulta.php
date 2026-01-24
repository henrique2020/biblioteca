<?php

use App\Biblioteca\Livro;

$livro = Livro::buscarPorSlug($slug, 'completo');
$erro = !$livro ? "Nenhum livro encontrado através da palavra chave '{$slug}'" : null;

$generos = "";
foreach ($livro->generos as $genero) {
    $escape = htmlspecialchars($genero->genero);
    $generos .= "<span class='badge text-bg-primary p-2 m-2'>{$escape}</span>";
}

$exemplares = "";
foreach ($livro->exemplares as $exemplar) {
    $escape = htmlspecialchars($exemplar->codigo);
    $color = $exemplar->status === 'D' ? 'success' : ($exemplar->status === 'E' ? 'warning' : 'danger');
    $exemplares .= "
        <tr>
            <td>{$escape}</td>
            <td>{$exemplar->dataAquisicao->format('d/m/Y')}</td>
            <td><span class='badge text-bg-{$color}'>{$exemplar->status_descritivo()}</span></td>
        </tr>
    ";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $livro ? htmlspecialchars($livro->livro) : 'Livro não encontrado' ?></title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/jquery/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <?php if ($erro) { ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Erro!</h4>
                <p><?= $erro ?></p>
                <hr>
                <a href="/" class="btn btn-primary">Voltar para Home</a>
            </div>
        <?php } else { ?>
            <!-- Informações do Livro -->
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title center text-center"><?= htmlspecialchars($livro->livro) ?></h1>
                    <p class="card-text text-muted text-center">
                        <?= htmlspecialchars($livro->autor) ?>
                        <br>
                        <?= $livro->dataLancamento->format('Y') ?>
                    </p>
                    <hr>
                    <p class="card-text">
                        <?= nl2br(htmlspecialchars($livro->descricao)) ?>
                    </p>
                    <hr>
                    <div id="acordeoes">
                        <p class="row justify-content-evenly">
                            <button class="col-lg-5 col-sm-12 btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#exemplares" aria-expanded="false" aria-controls="exemplares">Lista de exemplares (<?= count($livro->exemplares) ?>)</button>
                            <button class="col-lg-5 col-sm-12 btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#generos" aria-expanded="false" aria-controls="generos">Lista de gêneros (<?= count($livro->generos) ?>)</button>
                        </p>
                        <div class="row">
                            <!-- Exemplares -->
                            <div class="col-12">
                                <div class="collapse show" id="exemplares">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Data de Aquisição</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?= $exemplares ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Gêneros -->
                            <div class="col-12">
                                <div class="collapse" id="generos">
                                    <?= $generos ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a href="/" class="btn btn-secondary">Voltar</a>
        <?php } ?>
    </div>

    <script>
        $(function() {
            // Quando um acordeão abre, fecha os outros
            $('#acordeoes .collapse').on('show.bs.collapse', function () {
                $('#acordeoes .collapse').not(this).collapse('hide');
            });
        });
    </script>
</body>
</html>
