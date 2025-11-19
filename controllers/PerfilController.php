<?php

include_once(__DIR__ . "/../model/PerfilModel.php");

class PerfilController {

    private $conexion;
    private $renderer;
    private $perfilModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->perfilModel = new PerfilModel($conexion);
    }

    private function verificarLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["id_usuario"])) {
            header("Location: " . BASE_URL . "?error=no_login");
            exit();
        }
    }

    public function verPerfil() {
    $this->verificarLogin();

    $idUsuario = $_SESSION["id_usuario"];
    $usuarioPerfil = $this->perfilModel->obtenerUsuarioPorId($idUsuario);

    if (!$usuarioPerfil) {
        die("Error: no se encontró el usuario.");
    }

    // defaults
    $usuarioPerfil["foto_perfil"] = $usuarioPerfil["foto_perfil"] ?: "default.png";
    $usuarioPerfil["anio_nacimiento"] = $usuarioPerfil["anio_nacimiento"] ?: "—";
    $usuarioPerfil["sexo"] = $usuarioPerfil["sexo"] ?: "—";
    $usuarioPerfil["puntaje_maximo_obtenido"] = $usuarioPerfil["puntaje_maximo_obtenido"] ?: 0;

    $data = [
        "usuario" => $usuarioPerfil, // nav + datos del mismo usuario
        "perfil" => $usuarioPerfil,  // perfil del usuario
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("perfilUsuario", $data);
}



    public function verPerfilPublico() {

    if (!isset($_GET["id"])) {
        die("Usuario no especificado.");
    }

    $id = intval($_GET["id"]);

    $perfil = $this->perfilModel->obtenerUsuarioPorId($id);

    if (!$perfil) {
        die("El usuario no existe.");
    }

    // defaults
    $perfil["foto_perfil"] = $perfil["foto_perfil"] ?: "default.png";
    $perfil["anio_nacimiento"] = $perfil["anio_nacimiento"] ?: "—";
    $perfil["sexo"] = $perfil["sexo"] ?: "—";
    $perfil["puntaje_maximo_obtenido"] = $perfil["puntaje_maximo_obtenido"] ?: 0;

    // usuario logueado → PARA EL NAV
    session_start();
    $usuarioLogueado = null;
    if (isset($_SESSION["id_usuario"])) {
        $usuarioLogueado = $this->perfilModel->obtenerUsuarioPorId($_SESSION["id_usuario"]);
    }

    $data = [
        "usuario" => $usuarioLogueado, // nav siempre usa este
        "perfil" => $perfil,           // perfil del usuario visitado
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("perfilUsuario", $data);
}




}
