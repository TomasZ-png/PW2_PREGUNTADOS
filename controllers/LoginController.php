<?php

include_once(__DIR__."/../model/LoginModel.php");
include_once(__DIR__."/../config/config.php");

class LoginController{

private $conexion;
private $renderer;
private $loginModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->loginModel = new LoginModel($this->conexion);
    }

    public function loginForm(){
        $this->renderer->renderWoHaF('login', [
            "BASE_URL" => BASE_URL]);
    }

    public function registrarseForm(){
        $this->renderer->renderWoHaF('registrarse', [
            "BASE_URL" => BASE_URL]);
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
                    $_SESSION["nombre_usuario"] = $resultado['nombre_completo'];
                    $_SESSION["id_usuario"] = $resultado['id_usuario'];
                    $_SESSION["rol"] = $resultado['rol'];

                    header("Location: ". BASE_URL);
                    exit();
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

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $nombre = $_POST["name"];
            $fecha_nac = $_POST["fecha_nac"];
            $sexo = isset($_POST["sexo"]) ? $_POST["sexo"] : "";
            $email = $_POST["email"];
            $password = $_POST["password"];
            $foto_perfil = isset($_FILES["user_photo"]) ? $_FILES["user_photo"]["name"] : null;

            $resultado = $this->loginModel->registrarse($nombre, $fecha_nac, $sexo, $email, $password, $foto_perfil);

            if($resultado['exito']){
                $usuario = $resultado['usuario'];
                $_SESSION["nombre_usuario"] = $usuario['nombre_completo'];
                $_SESSION["id_usuario"] = $usuario['id_usuario'];
                $_SESSION["rol_usuario"] = $usuario['nombre_completo'];
                $this->redirectToHome();
            } else {
                $this->renderer->renderWoHaF('registrarse', ['errores' => $resultado['errores'], "BASE_URL" => BASE_URL]);
            }
        } else {
            $this->registrarseForm();
        }
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