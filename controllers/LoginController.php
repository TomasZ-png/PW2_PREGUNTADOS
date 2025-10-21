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

    private function usuarioLogueado(){
        return isset($_SESSION['id_usuario']);
    }

    public function login(){
        $this->renderer->renderWoHeader("login");

        if($this->usuarioLogueado()){
            header("Location: ". BASE_URL . "HomeController/mostrarHome");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $correo = $_POST["email"];
            $password = $_POST["password"];

            if(empty($correo) || empty($password)){
                echo "<p class='errores'>*Todos los campos son obligatorios</p>";
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
                    echo "<p class='errores'>*Correo o contraseña incorrectos</p>";
                }
            }
        }
    }

    public function registrarse(){

        if($this->usuarioLogueado()){
            header("Location: HomeController/mostrarHome");
            exit();
        }

        $this->renderer->renderWoHeader("registrarse");

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
                header("Location: ". BASE_URL . "HomeController/mostrarHome");
            }
        }

    }


    public function logout(){
        session_destroy();
        header("Location: ". BASE_URL . "LoginController/login");
        exit();
    }
}