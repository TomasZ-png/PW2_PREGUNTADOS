<?php

namespace services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class EmailService
{

    public function enviarCorreoDeVerificacion($email, $token){
        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.brevo.com';
            $mail->SMTPAuth = true;
            $mail->Username = '9bda9b001@smtp-brevo.com';   // usuario SMTP
            $mail->Password = '**********YAQ219';         // clave SMTP
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('preguntameprograweb2@gmail.com', 'PreguntameInc');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta de Preguntame';

            $link = BASE_URL . "LoginController/verificar?token=$token";

            $mail->Body = "
                <h2>Bienvenido!</h2>
                <p>Para activar tu cuenta hacé clic acá:</p>
                <a href='$link'>$link</a>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

}