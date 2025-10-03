<?php
// /security/EmailHelper.php

// Carrega as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailHelper {
    /**
     * Envia um e-mail de alerta quando o modo pânico é ativado.
     * @param array $panicInfo Dados detalhados sobre o evento de pânico.
     */
    public static function sendPanicAlert(array $panicInfo) {
        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURAÇÕES DO SEU SERVIDOR DE E-MAIL (SMTP) ---
            // Nenhuma alteração na sua lógica original.
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lucenamatheus620@gmail.com';
            $mail->Password   = 'ouivujxbggpxvyqb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            // --- FIM DA CONFIGURAÇÃO ---

            // Destinatários (lógica original)
            $mail->setFrom('lucenamatheus620@gmail.com', 'Alerta de Segurança');
            $mail->addAddress('lucenamatheus620@icloud.com');

            // --- CONTEÚDO DO E-MAIL (A ÚNICA PARTE ALTERADA) ---
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'ALERTA DE SEGURANÇA: Modo Pânico Ativado!';
            
            // Usando a sintaxe HEREDOC para um HTML mais limpo e organizado.
            $mail->Body = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px; overflow: hidden; }
                    .header { background-color: #d9534f; color: #ffffff; padding: 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 24px; }
                    .content { padding: 30px; color: #333333; line-height: 1.6; }
                    .content p { margin: 0 0 15px; }
                    .details-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    .details-table th, .details-table td { border: 1px solid #dddddd; padding: 12px; text-align: left; }
                    .details-table th { background-color: #f8f8f8; font-weight: bold; width: 35%; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Alerta de Segurança Crítico</h1>
                    </div>
                    <div class="content">
                        <p><strong>Atenção:</strong> O modo pânico do seu Assistente Pessoal foi ativado.</p>
                        <p>O sistema foi bloqueado como medida de proteção devido a múltiplas tentativas de login que falharam. Abaixo estão os detalhes do evento que acionou o alerta:</p>
                        <table class="details-table">
                            <tr>
                                <th>Data e Hora</th>
                                <td>{$panicInfo['triggered_at']} (Horário de Brasília)</td>
                            </tr>
                            <tr>
                                <th>Nome de Usuário</th>
                                <td>{$panicInfo['attempted_username']}</td>
                            </tr>
                            <tr>
                                <th>Endereço de IP</th>
                                <td>{$panicInfo['source_ip']}</td>
                            </tr>
                            <tr>
                                <th>Navegador/Sistema</th>
                                <td>{$panicInfo['user_agent']}</td>
                            </tr>
                        </table>
                        <p style="margin-top: 25px;"><strong>Ação Imediata:</strong> O acesso ao sistema e ao usuário do banco de dados foram travados. A reativação requer sua intervenção manual como administrador.</p>
                    </div>
                    <div class="footer">
                        <p>Esta é uma mensagem automática de segurança. Não responda a este e-mail.</p>
                    </div>
                </div>
            </body>
            </html>
HTML;
            // --- FIM DA ALTERAÇÃO NO CONTEÚDO ---

            $mail->send();
            Logger::log('INFO', "E-mail de alerta de pânico enviado com sucesso.");
        } catch (Exception $e) {
            // Lógica de erro original
            Logger::log('ERROR', "Falha ao enviar e-mail de alerta: {$mail->ErrorInfo}");
        }
    }
}