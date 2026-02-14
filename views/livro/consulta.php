<?php

use App\Biblioteca\Livro;

$livro = Livro::buscarPorSlug($slug, 'completo');
$generos = "";
$exemplares = "";
if($livro){
    foreach ($livro->generos as $genero) {
        $escape = htmlspecialchars($genero->genero);
        $generos .= "<span class='badge text-bg-primary p-2 m-2'>{$escape}</span>";
    }

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
}
$erro = !$livro ? "Nenhum livro encontrado através da palavra chave '{$slug}'" : null;
$titulo = $livro ? htmlspecialchars($livro->livro) : 'Livro não encontrado';

$lista_navegacao = [
    ['nome' => 'Home', 'link' => '/'],
    ['nome' => "Livro: {$titulo}", 'link' => '']
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php require_once view_path('layout/head.php'); ?>
    <title><?= $titulo ?></title>
</head>
<body>
    <?php require_once view_path('layout/nav.php'); ?>

    <main class="container mt-5">
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
                    <h1 class="card-title center text-center"><?= $titulo ?></h1>
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

            <div class="row justify-content-between">
                <div class="col">
                    <a href="/" class="btn btn-secondary">Voltar</a>
                </div>
                <div class="col text-end">
                    <a href="/livro/editar/<?= $livro->id ?>" class="col btn btn-primary"><i class="bi bi-pencil-square"></i> Editar</a>
                    <a href="#" class="col btn btn-danger"><i class="bi bi-trash"></i> Excluir</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </main>

    <?php require_once view_path('layout/footer.php'); ?>
    <script>
        const token = localStorage.getItem('token');

        $(function() {
            // Quando um acordeão abre, fecha os outros
            $('#acordeoes .collapse').on('show.bs.collapse', function () {
                $('#acordeoes .collapse').not(this).collapse('hide');
            });
        });
    </script>
</body>
</html>
