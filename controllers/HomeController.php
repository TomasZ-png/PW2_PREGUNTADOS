<?php

include_once(__DIR__ . "/../model/HomeModel.php");
include_once(__DIR__ . "/../model/UsuarioModel.php");

class HomeController {

    private $conexion;
    private $renderer;
    private $homeModel;
    private $usuarioModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->homeModel = new HomeModel($this->conexion);
        $this->usuarioModel = new UsuarioModel($this->conexion);
    }

    // 🔹 Redirige a login si no hay sesión activa
    private function redirectToLogin() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "/LoginController/login");
            exit();
        }
    }

    // 🔹 Muestra el Home
    public function mostrarHome() {
        $this->redirectToLogin();

        $id_usuario = $_SESSION['id_usuario'] ?? null;

        $usuario = $this->homeModel->obtenerUsuario($id_usuario);
        $usuarios = $this->usuarioModel->listarUsuarios();

        // ✅ Si hay mensajes almacenados en sesión (de PreguntaController, etc.)
        $data = [
            'usuarios' => $usuarios,
            'usuario' => $usuario,
            'esJugador' => false,
            'esAdmin' => false,
            'esEditor' => false
        ];

        if ($usuario && isset($usuario['rol'])) {
            $rol = strtoupper(trim($usuario['rol']));
            $data['esJugador'] = ($rol === 'USER');
        }

        if ($usuario && isset($usuario['rol'])) {
            $rol = strtoupper(trim($usuario['rol']));
            $data['esAdmin'] = ($rol === 'ADMIN');
        }
        if ($usuario && isset($usuario['rol'])) {
            $rol = strtoupper(trim($usuario['rol']));
            $data['esEditor'] = ($rol === 'EDITOR');
        }   


        if (isset($_SESSION['mensaje_home'])) {
            $data['mensaje_home'] = $_SESSION['mensaje_home'];
            unset($_SESSION['mensaje_home']); // limpia mensaje después de mostrarlo
        }

        if (isset($_SESSION['error_home'])) {
            $data['error_home'] = $_SESSION['error_home'];
            unset($_SESSION['error_home']);
        }

        if (isset($_GET['success'])) {
        $data['success'] = $_GET['success']; // 'reporte_enviado' o 'sugerencia_enviada'
    }

        return $this->renderer->render("home", $data);
    }

}
