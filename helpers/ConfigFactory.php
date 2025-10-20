<?php

include_once("MyDatabase.php");
include_once("../helpers/IncludeFileRenderer.php");
include_once('../helpers/Router.php');
include_once('../controllers/LoginController.php');
include_once('../controllers/HomeController.php');

class ConfigFactory{
    private $clases;

    public function __construct(){
        $this->clases['MyDatabase'] = new MyDatabase();
        $this->clases['Router'] = new Router($this, '', '');
        $this->clases['IncludeFileRenderer'] = new IncludeFileRenderer();
        $this->clases['LoginController'] = new LoginController($this->clases['MyDatabase'], $this->clases['IncludeFileRenderer']);
        $this->clases['HomeController'] = new HomeController($this->clases['MyDatabase'], $this->clases['IncludeFileRenderer']);
    }

    public function getClase($nombreClase){
        return isset($this->clases[$nombreClase]) ? $this->clases[$nombreClase] : null;
    }

}