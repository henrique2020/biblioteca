<?php
use App\Biblioteca\Livro;
$livros = Livro::listarTodos('completo');
$tbody = '';
foreach($livros as $livro){
    $slug = slugificar($livro->livro);
    $qtde = count($livro->exemplares);
    $tbody .= "
        <tr>
            <td><a href='livro/{$slug}'>{$livro->livro}</a></td>
            <td>{$livro->autor}</td>
            <td>{$qtde}</td>
        </tr>
    ";
}

if(empty($tbody)) {
    $tbody = '<tr><td colspan="3" class="text-center">Nenhum livro encontrado.</td></tr>';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php require_once view_path('layout/head.php'); ?>
    <title>Catálogo</title>
</head>
<body class="bg-light">
    <?php require_once view_path('layout/nav.php'); ?>

    <main class="container mt-5">
        <h1 class="mb-4">Catálogo de Livros</h1>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Livro</th>
                            <th>Autor</th>
                            <th>Exemplares</th>
                        </tr>
                    </thead>
                    <tbody id="livros">
                        <?= $tbody ?>  
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php require_once view_path('layout/footer.php'); ?>
    
    <script>
        const token = localStorage.getItem('token');

        async function loadUser() {
            const res = await fetch('/api/user', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (res.ok) {
                const user = await res.json();
                document.getElementById('userNameDisplay').textContent = `Olá, ${user.nome}`;
            } else {
                logout(); // Token inválido
            }
        }

        loadUser();
    </script>
</body>
</html>