<!--
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
-->

<?php
    // Se a variável $lista_navegacao não estiver definida, define como Home
    $lista_navegacao ??= [['nome' => 'Home', 'link' => '/']];
    $navegacao = "";
    $itens = count($lista_navegacao);
    foreach($lista_navegacao as $k => $v){
        $ultimo = ($k === $itens - 1);
        if(!$ultimo){
            $navegacao .= "<li class='breadcrumb-item'><a href='{$v['link']}'>{$v['nome']}</a></li>";
        } else {
            $navegacao .= "<li class='breadcrumb-item active' aria-current='page'>{$v['nome']}</li>";
        }
    }
?>


<style>
    nav div { max-height: 40px; }
    ol.breadcrumb { 
        & li, a, li::before { color: white !important; }
    }
</style>

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="d-flex justify-content-between px-4 w-100">
        <span class="navbar-brand">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?= $navegacao ?>
                </ol>
            </nav>
        </span>
        <div>
            
            <button onclick="logout()" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Sair</button>
        </div>
    </div>
</nav>