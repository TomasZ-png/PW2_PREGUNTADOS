<?php

include_once(__DIR__ . "/../model/PerfilModel.php");

class PerfilController {

    private $conexion;
    private $renderer;
    private $perfilModel;
    private $usuarioModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->perfilModel = new PerfilModel($conexion);
        $this->usuarioModel = new UsuarioModel($conexion);
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

    $direccionUsuario = $this->perfilModel->obtenerDireccionByIdUsuario($idUsuario);
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
        "direccion" => $direccionUsuario,
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("perfilUsuario", $data);
}



    public function verPerfilPublico() {

    if (!isset($_GET["id"])) {
        die("Usuario no especificado.");
    }

    $id = intval($_GET["id"]);

    $direccionUsuario = $this->perfilModel->obtenerDireccionByIdUsuario($id);

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
        "perfil" => $perfil,
        "direccion" => $direccionUsuario,
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("perfilUsuario", $data);
}

    public function mostrarHistorialDePartidas() {
        $this->verificarLogin();

        $idUsuario = $_GET["id_usuario"];
        $partidasJugadas = $this->usuarioModel->obtenerPartidasDelUsuario($idUsuario);

        foreach ($partidasJugadas as &$partida) {
            // Agregar icono según estado
            switch(strtolower($partida['estado_partida'])) {
                case 'abandonada':
                case 'perdida_por_recarga':
                    $partida['icono_estado'] = '🚫';
                    break;
                case 'en-curso':
                case 'en curso':
                    $partida['icono_estado'] = '⏳';
                    break;
                default:
                    $partida['icono_estado'] = '❌';
            }

            // Calcular duración si tienes las fechas
            if (!empty($partida['fecha_creacion']) && !empty($partida['fecha_finalizacion'])) {
                $inicio = new DateTime($partida['fecha_creacion']);
                $fin = new DateTime($partida['fecha_finalizacion']);
                $duracion = $inicio->diff($fin);

                // Guardar duración en segundos para ordenar
                $partida['duracion_segundos'] = ($duracion->days * 86400) +
                    ($duracion->h * 3600) +
                    ($duracion->i * 60) +
                    $duracion->s;

                // Formatear duración para mostrar
                if ($duracion->days > 0) {
                    $partida['duracion'] = $duracion->days . ' día' . ($duracion->days > 1 ? 's' : '');
                } elseif ($duracion->h > 0) {
                    $partida['duracion'] = $duracion->h . 'h ' . $duracion->i . 'm';
                } elseif ($duracion->i > 0) {
                    $partida['duracion'] = $duracion->i . ' min ' . $duracion->s . 's';
                } else {
                    $partida['duracion'] = $duracion->s . ' seg';
                }
            } else {
                $partida['duracion'] = 'N/A';
                $partida['duracion_segundos'] = 0;
            }
        }
        unset($partida);

        $puntajes = array_column($partidasJugadas, 'puntaje_final');
        $mejor_puntaje = !empty($puntajes) ? max($puntajes) : 0;
        $promedio_puntaje = !empty($puntajes) ? round(array_sum($puntajes) / count($puntajes)) : 0;

        $data = [
            "partidas" => $partidasJugadas,
            "BASE_URL" => BASE_URL,
            'total_partidas' => count($partidasJugadas),
            'mejor_puntaje' => $mejor_puntaje,
            'promedio_puntaje' => $promedio_puntaje,
        ];

        $this->renderer->render("historialDePartidas", $data);
    }


}
