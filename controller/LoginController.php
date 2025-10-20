<?php

class LoginController
{

    private $conexion;
    private $loginModel;
    private $renderer;

    public function __construct($conexion, $renderer){
        $this->renderer = $renderer;
        $this->conexion = $conexion;
        $this->loginModel = new LoginModel($this->conexion);
    }

    public function base()
    {
        $this->login();
    }

    public function loginForm()
    {
        $this->renderer->render("login");
    }

    public function login(){
    //  Validar que los campos existan
    if (!isset($_POST["usuario"]) || !isset($_POST["password"])) {
         $this->renderer->render("login", ["error" => "Debe ingresar usuario y clave"]);
         return; 
    }
    
    // El modelo devuelve array (éxito) o false (fallo)
    $resultado = $this->model->getUserWith($_POST["usuario"], $_POST["password"]);

    if ($resultado) { // Si $resultado no es false (es un array con datos de usuario)
         // Es mejor guardar el ID y el nombre, no solo el usuario
         $_SESSION["usuario"] = $resultado["usuario"]; 
         $_SESSION["nombre"] = $resultado["nombre"]; 
         $this->redirectToIndex();
    } else {
         $this->renderer->render("login", ["error" => "Usuario o clave incorrecta"]);
    }
}

    public function logout()
    {
        session_destroy();
        $this->redirectToIndex();
    }

    public function redirectToIndex()
    {
        header("Location: /");
        exit;
    }

}