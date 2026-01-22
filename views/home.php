<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/catalogo">Cátalogo</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="userNameDisplay">Carregando...</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/perfil">Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="logout()">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
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
                        <?php
                        use App\Biblioteca\Livro;
                        $livros = Livro::listarTodos('completo');
                        foreach($livros as $livro){
                            $slug = slugificar($livro->livro);
                            $qtde = count($livro->exemplares);
                            echo "
                                <tr>
                                    <td><a href='livro/{$slug}'>{$livro->livro}</a></td>
                                    <td>{$livro->autor}</td>
                                    <td>{$qtde}</td>
                                </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <p id="emptyMsg" class="text-center text-muted mt-4 d-none">Nenhum chamado encontrado.</p>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/login';

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

        function logout() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }

        function showAlert(msg, type) {
            const el = document.getElementById('alertBox');
            el.className = `alert alert-${type}`;
            el.textContent = msg;
            el.classList.remove('d-none');
        }

        loadUser();
    </script>
</body>
</html>