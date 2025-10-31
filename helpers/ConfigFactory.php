<?php

include_once("MyDatabase.php");
include_once(__DIR__ . "/IncludeFileRenderer.php");
include_once(__DIR__ . '/Router.php');
include_once(__DIR__ . '/../controllers/LoginController.php');
include_once(__DIR__.'/../controllers/HomeController.php');
include_once(__DIR__.'/../helpers/MustacheRenderer.php');
include_once(__DIR__.'/../vendor/mustache/src/Mustache/Autoloader.php');
include_once(__DIR__.'/../controllers/PartidaController.php'); 
include_once(__DIR__.'/../model/PartidaModel.php');
include_once(__DIR__.'/../model/UsuarioModel.php');

class ConfigFactory{
    private $clases;
    private $renderer;

    public function __construct(){
        Mustache_Autoloader::register();
        $this->renderer = new MustacheRenderer('views');
        $this->clases['MyDatabase'] = new MyDatabase();
        $this->clases['Router'] = new Router($this, 'HomeController', 'mostrarHome');
        $this->clases['LoginController'] = new LoginController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['HomeController'] = new HomeController($this->clases['MyDatabase'], $this->renderer);
        
        $this->clases['PartidaModel'] = new PartidaModel($this->clases['MyDatabase']); 

        // ¡LÍNEA CORREGIDA! Pasamos los 3 argumentos que espera el PartidaController
        $this->clases['PartidaController'] = new PartidaController(
            $this->clases['PartidaModel'],
            $this->renderer,
            $this->clases['MyDatabase']
        );
    }

    public function getClase($nombreClase){
        return isset($this->clases[$nombreClase]) ? $this->clases[$nombreClase] : null;
    }

}