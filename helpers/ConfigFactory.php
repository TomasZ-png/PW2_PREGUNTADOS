<?php

// ASEGÚRATE DE INCLUIR TODAS LAS CLASES
include_once ("MyDatabase.php");
include_once ("LoginModel.php");
include_once ("LoginController.php");
include_once ("Router.php");
include_once ("MustacheRenderer.php"); 
class ConfigFactory{

    private $clases;

    public function __construct(){
        $db = new MyDatabase();
        
        // 1. UTILIDADES
        $this->clases['MyDatabase'] = $db;
        $this->clases['Renderer'] = new MustacheRenderer('vista/'); // Ruta a la carpeta de vistas
        
        // 2. MODELO (Necesita la conexión a DB)
        $this->clases['LoginModel'] = new LoginModel($db);
        
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