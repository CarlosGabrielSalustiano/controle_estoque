<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function enviarEmailAlerta($para, $assunto, $mensagem) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'ResponsavelDaEmpresa@gmail.com';       // <-- Seu Gmail de testes
        $mail->Password   = 'qxhgjehroiguepqg';     // <-- Código de 16 letras gerado no Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        $mail->CharSet = 'pt-br'; 

        $mail->setFrom('ResponsavelDaEmpresa@gmail.com', 'STOCKFY Alertas');
        $mail->addAddress($para);

        $mail->isHTML(false); 
        $mail->Subject = $assunto;
        $mail->Body    = $mensagem;

        $mail->send();
    } catch (Exception $e) {
        error_log("Erro no e-mail STOCKFY: {$mail->ErrorInfo}");
    }
}
?>