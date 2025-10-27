<?php

include_once(__DIR__."/../model/PartidaModel.php");

class PartidaController{

    private $conexion;
    private $renderer;
    private $partidaModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->partidaModel = new PartidaModel($this->conexion);
    }

    private function inicarPartidaRender(){
        $this->renderer->render("partida");
    }

    private function cargarCategoria(){
        $categorias = $this->partidaModel->iniciarPartidaCategoria();
        $clave_categoria_a_usar = array_rand($categorias);
        return $categorias[$clave_categoria_a_usar];
    }

    public function iniciarPartida(){
        $this->redirectToLogin();

        $categorias_usadas = [];

        $categoria = $this->cargarCategoria();

        $preguntas = $this->partidaModel->generarPreguntasPorCategoria($categoria);

        echo $categoria;
    }

    private function redirectToLogin(){
        if(!isset($_SESSION['id_usuario'])){
            header("Location: ". BASE_URL . "LoginController/login");
            exit();
        }
    }

    private function redirectToHome(){}
}
