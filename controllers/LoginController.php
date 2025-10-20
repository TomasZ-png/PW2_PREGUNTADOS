<?php

include_once("../model/LoginModel.php");

class LoginController{

private $conexion;
private $renderer;
private $loginModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->loginModel = new LoginModel($this->conexion);
    }

    public function login(){
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
                } else {
                    echo "Correo o contraseña incorrectos";
                }
            }



        }
        $this->renderer->renderWoHeader("login.html");
    }

}