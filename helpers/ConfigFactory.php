<?php

include_once("MyDatabase.php");
include_once(__DIR__ . "/IncludeFileRenderer.php");
include_once(__DIR__ . '/Router.php');
include_once(__DIR__ . '/../controllers/LoginController.php');
include_once(__DIR__.'/../controllers/HomeController.php');

class ConfigFactory{
    private $clases;

    public function __construct(){
        $this->clases['MyDatabase'] = new MyDatabase();
        $this->clases['Router'] = new Router($this, 'HomeController', 'mostrarHome');
        $this->clases['IncludeFileRenderer'] = new IncludeFileRenderer();
        $this->clases['LoginController'] = new LoginController($this->clases['MyDatabase'], $this->clases['IncludeFileRenderer']);
        $this->clases['HomeController'] = new HomeController($this->clases['MyDatabase'], $this->clases['IncludeFileRenderer']);
    }

    public function getClase($nombreClase){
        return isset($this->clases[$nombreClase]) ? $this->clases[$nombreClase] : null;
    }

}