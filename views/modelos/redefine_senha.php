<h3>Olá, <?= $nome ?>!</h3>
<p>Recebemos uma solicitação para redefinir a senha da sua conta.</p>
<p>Para continuar, clique no botão abaixo:</p>

<a href="<?= $link ?>" class="button">🔐 Redefinir senha</a>

<p>
    <b>⏱️ Importante:</b> Este link é válido por <?= $validade ?>.
    <br>
    Após esse período, será necessário solicitar uma nova redefinição.
</p>

<p>Se você não solicitou esta redefinição de senha, por favor ignore este e-mail.</p>