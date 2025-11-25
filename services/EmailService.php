<?php


namespace services;

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/env_loader.php';
loadEnv(__DIR__ . '/../.env');

include_once(__DIR__ . '/../config/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    public function enviarEmailVerificacion($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP para InfinityFree
            $mail->isSMTP();
            $mail->Host = getenv('SMTP_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER');
            $mail->Password = getenv('API_KEY');

            // CAMBIO CRÍTICO: Usar SSL en puerto 465
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Opciones SSL para servidores restrictivos
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Debug (comentar en producción)
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Remitente
            $mail->setFrom('preguntameprograweb2@gmail.com', 'PreguntameInc');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta';

            $link = BASE_URL . "LoginController/verificarCuenta?token=$token";

            $mail->Body = "
                <table width='100%' cellpadding='0' cellspacing='0' style='font-family: Arial, sans-serif; background:#f5f5f5; padding: 20px;'>
    <tr>
        <td align='center'>
            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
                <tr>
                    <td style='background:#4E73DF; padding:20px; color:white; text-align:center;'>
                        <h1 style='margin:0; font-size:24px;'>¡Bienvenido a Preguntame!</h1>
                    </td>
                </tr>

                <tr>
                    <td style='padding:25px; color:#333;'>
                        <p style='font-size:16px;'>Hola 👋</p>
                        <p style='font-size:16px; line-height:1.5;'>
                            Gracias por registrarte. Para activar tu cuenta hacé click en el botón de abajo.
                        </p>

                        <div style='text-align:center; margin-top:30px;'>
                            <a href='$link' 
                               style='background:#4E73DF; color:white; padding:14px 22px; 
                               text-decoration:none; border-radius:6px; font-size:16px; display:inline-block;'>
                                Verificar cuenta
                            </a>
                        </div>

                        <p style='margin-top:30px; font-size:14px; color:#666;'>
                            Si el botón no funciona, podés copiar este enlace en tu navegador:
                        </p>

                        <p style='word-break:break-all; font-size:14px; color:#4E73DF;'>
                            $link
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style='background:#f0f0f0; padding:15px; text-align:center; font-size:13px; color:#777;'>
                        © " . date('Y') . " PreguntameInc — Todos los derechos reservados.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            // Log detallado para debugging
            error_log("=== ERROR PHPMAILER ===");
            error_log("Mensaje: " . $e->getMessage());
            error_log("Host: " . getenv('SMTP_HOST'));
            error_log("Puerto: " . getenv('SMTP_PORT'));
            error_log("Usuario: " . getenv('SMTP_USER'));
            error_log("API Key presente: " . (getenv('API_KEY') ? 'Sí' : 'No'));
            error_log("======================");
            return false;
        }
    }
}


//namespace services;
//
//require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
//require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
//require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
//require_once __DIR__ . '/env_loader.php';
//loadEnv(__DIR__ . '/../.env');
//
//include_once(__DIR__ . '/../config/config.php');
//
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
//use PHPMailer\PHPMailer\Exception;
//
//class EmailService
//{
//    public function enviarEmailVerificacion($email, $token)
//    {
//        $mail = new PHPMailer(true);
//
//        try {
//            $mail->isSMTP();
//            $mail->Host = getenv('SMTP_HOST');
//            $mail->SMTPAuth = true;
//            $mail->Username = getenv('SMTP_USER');
//            $mail->Password = getenv('API_KEY');
//            $mail->SMTPSecure = 'tls';
//            $mail->Port = getenv('SMTP_PORT');
//
//            $mail->CharSet = 'UTF-8';
//            $mail->Encoding = 'base64';
//
//
//            // Remitente
//            $mail->setFrom('preguntameprograweb2@gmail.com', 'PreguntameInc');
//            $mail->addAddress($email);
//
//            $mail->isHTML(true);
//            $mail->Subject = 'Verifica tu cuenta';
//
//            $link = BASE_URL . "LoginController/verificarCuenta?token=$token";
//
//            $mail->Body = "
//                <table width='100%' cellpadding='0' cellspacing='0' style='font-family: Arial, sans-serif; background:#f5f5f5; padding: 20px;'>
//    <tr>
//        <td align='center'>
//            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
//                <tr>
//                    <td style='background:#4E73DF; padding:20px; color:white; text-align:center;'>
//                        <h1 style='margin:0; font-size:24px;'>¡Bienvenido a Preguntame!</h1>
//                    </td>
//                </tr>
//
//                <tr>
//                    <td style='padding:25px; color:#333;'>
//                        <p style='font-size:16px;'>Hola 👋</p>
//                        <p style='font-size:16px; line-height:1.5;'>
//                            Gracias por registrarte. Para activar tu cuenta hacé click en el botón de abajo.
//                        </p>
//
//                        <div style='text-align:center; margin-top:30px;'>
//                            <a href='$link'
//                               style='background:#4E73DF; color:white; padding:14px 22px;
//                               text-decoration:none; border-radius:6px; font-size:16px; display:inline-block;'>
//                                Verificar cuenta
//                            </a>
//                        </div>
//
//                        <p style='margin-top:30px; font-size:14px; color:#666;'>
//                            Si el botón no funciona, podés copiar este enlace en tu navegador:
//                        </p>
//
//                        <p style='word-break:break-all; font-size:14px; color:#4E73DF;'>
//                            $link
//                        </p>
//                    </td>
//                </tr>
//
//                <tr>
//                    <td style='background:#f0f0f0; padding:15px; text-align:center; font-size:13px; color:#777;'>
//                        © " . date('Y') . " PreguntameInc — Todos los derechos reservados.
//                    </td>
//                </tr>
//            </table>
//        </td>
//    </tr>
//</table>
//            ";
//
//            $mail->send();
//            return true;
//
//        } catch (Exception $e) {
//            error_log("ERROR EN EMAIL: " . $e->getMessage());
//            return false;
//        }
//    }
//}

//namespace services;
//
//require_once __DIR__ . '/env_loader.php';
//loadEnv(__DIR__ . '/../.env');
//
//include_once(__DIR__ . '/../config/config.php');
//
//class EmailService
//{
//    public function enviarEmailVerificacion($email, $token)
//    {
//        {
//            $apiKey = getenv('BREVO_API_KEY');
//
//            $link = BASE_URL . "LoginController/verificarCuenta?token=$token";
//
//            $data = [
//                "sender" => ["name" => "PreguntameInc", "email" => "preguntameprograweb2@gmail.com"],
//                "to" => [["email" => $email]],
//                "subject" => "Verifica tu cuenta",
//                "htmlContent" => "
//                <table width='100%' cellpadding='0' cellspacing='0' style='font-family: Arial, sans-serif; background:#f5f5f5; padding: 20px;'>
//                    <tr>
//                        <td align='center'>
//                            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
//                                <tr>
//                                    <td style='background:#4E73DF; padding:20px; color:white; text-align:center;'>
//                                        <h1 style='margin:0; font-size:24px;'>¡Bienvenido a Preguntame!</h1>
//                                    </td>
//                                </tr>
//
//                                <tr>
//                                    <td style='padding:25px; color:#333;'>
//                                        <p style='font-size:16px;'>Hola 👋</p>
//                                        <p style='font-size:16px; line-height:1.5;'>
//                                            Gracias por registrarte. Para activar tu cuenta hacé click en el botón de abajo.
//                                        </p>
//
//                                        <div style='text-align:center; margin-top:30px;'>
//                                            <a href='$link'
//                                               style='background:#4E73DF; color:white; padding:14px 22px;
//                                               text-decoration:none; border-radius:6px; font-size:16px; display:inline-block;'>
//                                                Verificar cuenta
//                                            </a>
//                                        </div>
//
//                                        <p style='margin-top:30px; font-size:14px; color:#666;'>
//                                            Si el botón no funciona, podés copiar este enlace en tu navegador:
//                                        </p>
//
//                                        <p style='word-break:break-all; font-size:14px; color:#4E73DF;'>
//                                            $link
//                                        </p>
//                                    </td>
//                                </tr>
//
//                                <tr>
//                                    <td style='background:#f0f0f0; padding:15px; text-align:center; font-size:13px; color:#777;'>
//                                        © ".date('Y')." PreguntameInc — Todos los derechos reservados.
//                                    </td>
//                                </tr>
//                            </table>
//                        </td>
//                    </tr>
//                </table>
//            "
//            ];
//
//            $ch = curl_init("https://api.brevo.com/v3/smtp/email");
//            curl_setopt($ch, CURLOPT_HTTPHEADER, [
//                "accept: application/json",
//                "api-key: $apiKey",
//                "content-type: application/json"
//            ]);
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//            $response = curl_exec($ch);
//            $err = curl_error($ch);
//            curl_close($ch);
//
//            if ($err) {
//                error_log("Error CURL: $err");
//                return false;
//            }
//
//            return true;
//        }
//    }
//}