<?php

include_once(__DIR__."/../model/HomeModel.php");

class HomeController{

    private $conexion;
    private $renderer;
    private $homeModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->homeModel = new HomeModel($this->conexion);
    }

    private function renderHome(){
        $this->renderer->render('home');
    }

    public function mostrarHome(){

        $this->redirectToLogin();

        $this->renderHome();
    }


    private function redirectToLogin(){
        if(!isset($_SESSION['id_usuario'])){
            header("Location: ". BASE_URL . "LoginController/login");
            exit();
        }
    }

}