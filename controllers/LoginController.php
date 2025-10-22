<?php

include_once(__DIR__."/../model/LoginModel.php");

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
        $this->renderer->render('login');
    }

    public function registrarseForm(){
        $this->renderer->render('registrarse');
    }

    public function login(){
        $this->redirectToHome();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $correo = $_POST["email"];
            $password = $_POST["password"];

            if(empty($correo) || empty($password)){
                $this->renderer->render('login', ['passOrEmailEmpty' => '*Todos los campos son obligatorios']);
            } else {
                $resultado = $this->loginModel->login($correo, $password);

                if($resultado){
                    $_SESSION["nombre_usuario"] = $resultado['nombre_completo'];
                    $_SESSION["id_usuario"] = $resultado['id_usuario'];
                    $_SESSION["rol_usuario"] = $resultado['nombre_completo'];
                    echo "Logueado Correctamente";
                    header("Location: ". BASE_URL . "HomeController/mostrarHome");
                    exit();
                } else {
                    $this->renderer->render('login', ['passOrEmailWrong' => '*Correo o contraseña incorrectos']);
                }
            }
        }
    }

    public function registrarse(){
        $this->redirectToHome();

//        $this->renderer->renderWoHeader("registrarse");

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $nombre = $_POST["name"];
            $fecha_nac = $_POST["fecha_nac"];
            $sexo = isset($_POST["sexo"]) ? $_POST["sexo"] : "";
            $email = $_POST["email"];
            $password = $_POST["password"];
            $foto_perfil = isset($_FILES["user_photo"]) ? $_FILES["user_photo"]["name"] : null;

            $resultado = $this->loginModel->registrarse($nombre, $fecha_nac, $sexo, $email, $password, $foto_perfil);

            if($resultado != null){
                $_SESSION["nombre_usuario"] = $resultado['nombre_completo'];
                $_SESSION["id_usuario"] = $resultado['id_usuario'];
                $_SESSION["rol_usuario"] = $resultado['nombre_completo'];
                $this->redirectToHome();
            } else {
                $this->renderer->render('login', ['error' => 'Error al registrarse']);
            }
        }
    }

    public function logout(){
        session_destroy();
        header("Location: ". BASE_URL . "LoginController/login");
        exit();
    }

    private function redirectToHome(){
        if(isset($_SESSION['id_usuario'])){
            header("Location: ". BASE_URL . "HomeController/mostrarHome");
            exit();
        }
    }

}