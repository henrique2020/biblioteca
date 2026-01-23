# 🚀 Como Iniciar o projeto

### 1. Pré-requisitos

* PHP 8.1 ou superior
* Composer

### 2. Instalação de Dependências

No terminal, na raiz do projeto, execute:

```powershell
composer install
```

Este comando instalará as dependências e executará o script post-install para organizar os assets (Bootstrap, jQuery) na pasta `public/assets/`.

### 3. Configuração do Banco de Dados

1. Crie um banco de dados chamado `biblioteca`.
2. Importe o arquivo SQL localizado em: `src/Database/estrutura.sql`.

### 4. Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto  com as seguintes chaves:

**Snippet de código**

```environent
JWT_SECRET=uma_chave_secreta_e_longa

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

EMAIL_HOST=seu_smtp
EMAIL_PORT=587
EMAIL_USERNAME=seu_email
EMAIL_PASSWORD=sua_senha
EMAIL_APP_PASSWORD=senha_de_aplicativo
EMAIL_NAME="Biblioteca Central"
```

### 5. Executando a Aplicação

Você pode utilizar o servidor embutido do PHP:

```powershell
php -S localhost:8000 -t public
```

Acesse `http://localhost:8000` no seu navegador.
