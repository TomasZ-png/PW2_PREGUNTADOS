<?php

include_once ("MyDatabase.php");
include_once ("../model/modelLogin.php");
include_once ("../controller/LoginController.php");
include_once ("Router.php");
include_once ("MustacheRenderer.php"); 
class ConfigFactory{

    private $clases;

    public function __construct(){
        // 1. UTILIDADES
        $this->clases['MyDatabase'] = new MyDatabase();
        $this->clases['Renderer'] = new MustacheRenderer('vista/');
        
        // 2. MODELO (Necesita la conexión a DB)
        $this->clases['LoginModel'] = new LoginModel($this->clases['MyDatabase']);
        
        // 3. CONTROLADOR (Necesita su modelo y el renderer)
        $this->clases['LoginController'] = new LoginController(
            $this->clases['LoginModel'],
            $this->clases['Renderer']
        );
        
        // 4. ROUTER (Necesita valores por defecto)
        // Por defecto, carga el LoginController y su método loginForm
        $this->clases['Router'] = new Router($this, 'LoginController', 'loginForm');
    }

    public function getClase($nombreClase){
        return isset($this->clases[$nombreClase]) ? $this->clases[$nombreClase] : null;
    }
}