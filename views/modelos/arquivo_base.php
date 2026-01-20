<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f4f4; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #4a4a4a; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        .header { background-color: #2c3e50; padding: 20px; text-align: center; color: #ffffff; }
        .content { padding: 30px; line-height: 1.6; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #7f8c8d; }
        .button { display: inline-block; padding: 12px 25px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h2>Biblioteca Virtual</h2>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <?= $html ?>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; <?= date('Y') ?> Biblioteca - Todos os direitos reservados.<br>
                    Você recebeu este e-mail porque está cadastrado em nosso sistema.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>