<?php

include_once(__DIR__."/../model/HomeModel.php");

class HomeController{

    private $conexion;
    private $renderer;
    private $homeModel;
    private $usuarioModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->homeModel = new HomeModel($this->conexion);
        $this->usuarioModel = new UsuarioModel($this->conexion);
    }

    private function renderHome(){
        $this->renderer->render('home');
    }

    public function mostrarHome(){

        $this->redirectToLogin();

        $id_usuario = $_SESSION['id_usuario'] ? $_SESSION['id_usuario'] : null;

        $usuario = $this->homeModel->obtenerUsuario($id_usuario);

        $usuarios = $this->usuarioModel->listarUsuarios();

        return $this->renderer->render("home", ['usuarios' => $usuarios, 'usuario' => $usuario]);
    }


    private function redirectToLogin(){
        if(!isset($_SESSION['id_usuario'])){
            header("Location: ". BASE_URL . "LoginController/login");
            exit();
        }
    }

}