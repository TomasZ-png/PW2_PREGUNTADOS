<?php

use services\EmailService;

include_once(__DIR__."/../model/LoginModel.php");
include_once(__DIR__."/../config/config.php");
include_once(__DIR__."/../model/UsuarioModel.php");
include_once(__DIR__."/../services/EmailService.php");

class LoginController{

    private $conexion;
    private $renderer;
    private $loginModel;
    private $usuarioModel;
    private $emailService;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->loginModel = new LoginModel($this->conexion);
        $this->usuarioModel = new UsuarioModel($this->conexion);
        $this->emailService = new EmailService();
    }

    public function loginForm(){
        $this->renderer->renderWoHaF('login', [
            "BASE_URL" => BASE_URL]);
    }

    public function registrarseForm(){
        $this->renderer->renderWoHaF('registrarse', [
            "BASE_URL" => BASE_URL, 'BASE_URL_JSON' => json_encode(BASE_URL)]);
    }

    public function login(){
        $this->redirectToHome();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $correo = $_POST["email"];
            $password = $_POST["password"];

            if(empty($correo) || empty($password)){
                $this->renderer->renderWoHaF('login', ['passOrEmailEmpty' => '*Todos los campos son obligatorios', "BASE_URL" => BASE_URL]);
            } else {
                $resultado = $this->loginModel->login($correo, $password);

                if($resultado){
                    if($resultado["verificado"] === 1){

                        $_SESSION["nombre_usuario"] = $resultado['nombre_completo'];
                        $_SESSION["id_usuario"] = $resultado['id_usuario'];
                        $_SESSION["rol"] = $resultado['rol'];

                        header("Location: ". BASE_URL);
                        exit();
                    } else {
                        $this->renderer->renderWoHaF('login', ['passOrEmailWrong' => '*Tu cuenta no se encuentra verificada. Revisa tu bandeja de entrada de tu correo para verificarla.', "BASE_URL" => BASE_URL]);
                    }
                } else {
                    $this->renderer->renderWoHaF('login', ['passOrEmailWrong' => '*Correo o contraseña incorrectos', "BASE_URL" => BASE_URL]);
                }
            }
        } else {
            $this->loginForm();
        }
    }

    public function registrarse(){
        $this->redirectToHome();
        $this->renderer->renderWoHaF('registrarse', ['BASE_URL' => BASE_URL, 'BASE_URL_JSON' => json_encode(BASE_URL)]);
    }

    public function registrarseConAjax(){
        header('Content-type: application/json');

        $nombre = $_POST["name"];
        $fecha_nac = $_POST["fecha_nac"];
        $sexo = $_POST["sexo"] ?? "";
        $email = $_POST["email"];
        $password = $_POST["password"];
        $foto_perfil = isset($_FILES["user_photo"]) ? $_FILES["user_photo"]["name"] : null;
        $latidud = $_POST['latitud'];
        $longitud = $_POST['longitud'];
        $pais = $_POST['pais'];
        $ciudad = $_POST['ciudad'];

        $resultado = $this->loginModel->registrarse(
            $nombre,
            $fecha_nac,
            $sexo,
            $email,
            $password,
            $foto_perfil,
            $latidud,
            $longitud,
            $pais,
            $ciudad
        );

        if ($resultado['exito']) {
            try{
                $_SESSION['correo'] = $email;
                $token = bin2hex(random_bytes(32));
                $this->usuarioModel->guardarTokenVerificacion($email, $token);
                $this->emailService->enviarEmailVerificacion($email, $token);
            }catch (Exception $e){
                error_log("Exception EmailService: " . $e->getMessage());
                echo json_encode(["exito" => false, "errores" => ["Fallo enviando el correo."]]);
                return;
            }
            echo json_encode(["exito" => true]);
            return;
        }
        echo json_encode($resultado);
    }

    public function registroExitoso(){
        $this->renderer->renderWoHaF("verificacionDeCorreo", [
            "BASE_URL" => BASE_URL,
            "correo" => $_SESSION['correo']
        ]);
    }

    public function verificarCuenta(){
        if(!isset($_GET['token'])){
            echo 'Token invalido';
            return;
        }

        $token = $_GET['token'];
        $this->usuarioModel->verificarCuenta($token);

        $this->renderer->renderWoHaF('cuentaVerificada', ['BASE_URL' => BASE_URL]);
    }

    public function logout(){
        session_destroy();
        header("Location: ". BASE_URL . "LoginController/login");
        exit();
    }

    private function redirectToHome(){
        if(isset($_SESSION['id_usuario'])){
            header("Location: ". BASE_URL);
            exit();
        }
    }

}