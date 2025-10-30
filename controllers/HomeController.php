<?php

include_once(__DIR__."/../model/HomeModel.php");

class HomeController {

    private $conexion;
    private $renderer;
    private $homeModel;
    private $basePath = '/PROYECTO_PREGUNTADOS/'; // ✅ agregamos basePath

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->homeModel = new HomeModel($this->conexion);
    }

    // Render del home con basePath incluido
    private function renderHome() {
        $datos = [
            'basePath' => $this->basePath, // ✅ pasamos basePath a la vista
        ];
        $this->renderer->render('home', $datos);
    }

    public function mostrarHome() {
        $this->redirectToLogin();
        $this->renderHome();
    }

    private function redirectToLogin() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . $this->basePath . "LoginController/login");
            exit();
        }
    }
}
