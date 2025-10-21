<?php

include_once(__DIR__."/../model/HomeModel.php");

class HomeController
{

    private $conexion;
    private $renderer;
    private $homeModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->homeModel = new HomeModel($this->conexion);
    }


    private function usuarioLogueado(){
        return isset($_SESSION['id_usuario']);
    }

    public function mostrarHome(){

        if(!$this->usuarioLogueado()){
            header('location: index.php?controller=LoginController&method=login');
        }

        echo password_hash('1234', PASSWORD_DEFAULT);

        $this->renderer->renderWoHeader("home");
    }


}