<?php

namespace App\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private PHPMailer $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['EMAIL_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['EMAIL_USERNAME'];
        $this->mail->Password   = $_ENV['EMAIL_APP_PASSWORD'] ?? $_ENV['EMAIL_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $_ENV['EMAIL_PORT'] ?? 587;
        $this->mail->CharSet    = 'UTF-8';
        
        $this->mail->setFrom($_ENV['EMAIL_USERNAME'], $_ENV['EMAIL_NAME']);
    }

    private function setaDestinatarios(array $from, array $cc = [], array $cco = []): void {
        foreach ($from as $destinatario) {
            $this->mail->addAddress($destinatario['email'], $destinatario['nome'] ?? '');
        }

        foreach ($cc as $destinatario) {
            $this->mail->addCC($destinatario['email'], $destinatario['nome'] ?? '');
        }

        foreach ($cco as $destinatario) {
            $this->mail->addBCC($destinatario['email'], $destinatario['nome'] ?? '');
        }
    }

    private function limparEmail(): void {
        $this->mail->clearAllRecipients();  //Remove do FROM, CC e CCO
        $this->mail->clearAttachments();    //Remove os anexos
        $this->mail->Body = null;
        $this->mail->Subject = null;
    }

    private function renderizarHtml(string $template, array $data): string {
        $arquivoTemplate = view_path("modelos/{$template}.php");
        $arquivoBase = view_path('modelos/arquivo_base.php');
        
        if (!file_exists($arquivoTemplate)) {
            throw new \Exception("Template de e-mail não encontrado: {$template}");
        }

        extract($data);
        
        ob_start();
        include $arquivoTemplate;
        $html = ob_get_clean();
        
        ob_start();
        include $arquivoBase;
        return ob_get_clean();
    }

    public function enviar(array $json): bool {
        try {
            extract($json);
            $this->mail->Subject = $assunto;
            $this->setaDestinatarios($enviar_para['from'], $enviar_para['cc'] ?? [], $enviar_para['cco'] ?? []);

            // Conteúdo HTML
            $this->mail->isHTML(true);
            $this->mail->Body = $this->renderizarHtml($template, $atributos);

            return $this->mail->send();
            //return $this->debug();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        } finally {
            $this->limparEmail();
        }
    }

    public function debug(): bool {
        echo '<div style="border: 1px solid #ccc; padding: 15px; margin: 10px; font-family: sans-serif;">';
        echo '<strong>Assunto:</strong> ' . $this->mail->Subject . '<br><hr>';
        echo $this->mail->Body;
        echo '</div>';
        return true;
    }
}