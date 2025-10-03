<?php
// /security/ContactEmailHelper.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class ContactEmailHelper {
    /**
     * Envia um e-mail de contato estilizado como o React EmailPreview
     * @param string $fromEmail  E-mail do remetente
     * @param string $fromName   Nome do remetente
     * @param string $subject    Assunto da mensagem
     * @param string $message    Corpo da mensagem
     * @param string $userIP     IP do remetente
     * @param string $userAgent  User-Agent do remetente
     * @return bool
     */
    public static function sendContact($fromEmail, $fromName, $subject, $message, $userIP = '[Não informado]', $userAgent = '[Não informado]') {
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lucenamatheus620@gmail.com';
            $mail->Password   = 'ouivujxbggpxvyqb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Destinatário
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress('lucena.dev@icloud.com', 'Admin');

            // Formata datas
            $currentDate = date('l, d \d\e F \d\e Y H:i');

            // Corpo HTML
            $htmlBody = "
            <div style='font-family:sans-serif; max-width:600px; margin:auto;'>
                <div style='background:#f0f0f0; padding:20px; border-radius:8px;'>
                    <h2 style='margin:0; font-size:18px;'>Novo Email Recebido</h2>
                    <p style='margin:4px 0; color:#555;'>Visualização de como o email aparece na sua caixa de entrada</p>
                </div>
                <div style='margin-top:16px; border:1px solid #ddd; border-radius:8px; overflow:hidden;'>
                    <div style='background:#e0f2ff; border-left:4px solid #3b82f6; padding:16px;'>
                        <strong>{$fromName}</strong> &lt;{$fromEmail}&gt;<br>
                        <strong>Assunto:</strong> {$subject}<br><br>
                        <div style='white-space:pre-wrap;'>{$message}</div>
                        <hr>
                        <small style='color:#555;'>
                            <strong>Informações Técnicas:</strong><br>
                            IP do remetente: {$userIP}<br>
                            User-Agent: {$userAgent}<br>
                            Timestamp: ".date('c')."
                        </small>
                    </div>
                </div>
            </div>
            ";

            // Texto puro (AltBody)
            $altBody = "Assunto: {$subject}
De: {$fromName} <{$fromEmail}>
Para: lucena.dev@icloud.com
Data: {$currentDate}

Mensagem:
{$message}

---
Esta mensagem foi enviada através do formulário de contato do website.
IP do remetente: {$userIP}
User-Agent: {$userAgent}
Timestamp: ".date('c')."
";

            // Conteúdo do email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = nl2br($htmlBody);
            $mail->AltBody = $altBody;

            $mail->send();
            Logger::log('INFO', "E-mail de contato enviado por {$fromName} <{$fromEmail}>");
            return true;
        } catch (Exception $e) {
            Logger::log('ERROR', "Falha ao enviar e-mail de contato: {$mail->ErrorInfo}");
            return false;
        }
    }
}
