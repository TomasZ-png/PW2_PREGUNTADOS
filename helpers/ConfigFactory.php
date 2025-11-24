<?php

include_once("MyDatabase.php");
include_once(__DIR__ . "/IncludeFileRenderer.php");
include_once(__DIR__ . '/Router.php');
include_once(__DIR__ . '/../controllers/LoginController.php');
include_once(__DIR__.'/../controllers/HomeController.php');
include_once(__DIR__.'/../helpers/MustacheRenderer.php');
include_once(__DIR__.'/../vendor/mustache/src/Mustache/Autoloader.php');
include_once(__DIR__.'/../controllers/PartidaController.php'); 
include_once(__DIR__.'/../controllers/PreguntaController.php'); 
include_once(__DIR__.'/../controllers/AdminController.php');
include_once(__DIR__.'/../model/PartidaModel.php');
include_once(__DIR__.'/../model/UsuarioModel.php');
include_once(__DIR__.'/../model/PreguntaModel.php');
include_once(__DIR__.'/../controllers/ReportarPreguntaController.php');
include_once(__DIR__.'/../model/ReportarPreguntaModel.php');
include_once(__DIR__.'/../controllers/EditorController.php');
include_once(__DIR__.'/../model/EditorModel.php');
include_once(__DIR__.'/../controllers/PerfilController.php');
include_once(__DIR__.'/../model/PerfilModel.php');
include_once(__DIR__.'/../phpqrcode/qrlib.php');
include_once(__DIR__.'/../controllers/QRCodeController.php');
include_once(__DIR__.'/../public/vendor/amenadiel/jpgraph/src/graph.inc.php');
include_once(__DIR__ .'/../helpers/CategoryColorHelper.php');
include_once(__DIR__.'/../model/AdminModel.php');

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
        $this->clases['PreguntaController'] = new PreguntaController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['ReportarPreguntaController'] = new ReportarPreguntaController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['PartidaModel'] = new PartidaModel($this->clases['MyDatabase']);
        $this->clases['PreguntaModel'] = new PreguntaModel($this->clases['MyDatabase']);
        $this->clases['ReportarPreguntaModel'] = new ReportarPreguntaModel($this->clases['MyDatabase']);
        $this->clases['EditorController'] = new EditorController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['EditorModel'] = new EditorModel($this->clases['MyDatabase']);
        $this->clases['AdminController'] = new AdminController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['PerfilModel'] = new PerfilModel($this->clases['MyDatabase']);
        $this->clases['PerfilController'] = new PerfilController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['QRCodeController'] = new QRCodeController($this->clases['MyDatabase'], $this->renderer);
        $this->clases['AdminModel'] = new AdminModel($this->clases['MyDatabase']);

        //Pasamos los 3 argumentos que espera el PartidaController
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