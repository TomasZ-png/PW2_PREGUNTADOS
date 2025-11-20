<?php

include_once(__DIR__ . "/../model/ReportarPreguntaModel.php");

class ReportarPreguntaController {

    private $conexion;
    private $renderer;
    private $reportarPreguntaModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->reportarPreguntaModel = new ReportarPreguntaModel($conexion);
    }

    private function redirectToLogin() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "LoginController/login");
            exit();
        }
    }

    // Mostrar formulario con las preguntas jugadas
    public function mostrarFormulario() {
        $this->redirectToLogin();

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        $idPartida = $_GET['id_partida'] ?? null;

        if (!$idPartida) {
            header("Location: " . BASE_URL . "?error=no_partida");
            exit();
        }

        $preguntas = $this->reportarPreguntaModel->obtenerPreguntasDePartida($idPartida);

        if ($preguntas === false) {
            // Si el model devolvió false hubo un error interno: loguealo y redirige al home con mensaje
            error_log("ReportarPreguntaController: error al obtener preguntas de partida id_partida=$idPartida");
            header("Location: " . BASE_URL . "?error=internal");
            exit();
        }

        $data = [
            'id_partida' => $idPartida,
            'preguntas' => $preguntas,
            'basePath' => BASE_URL
        ];

        // render con datos
        $this->renderer->renderWoHaF("reportarPregunta", $data);
    }

    // Guardar el reporte
    public function guardarReporte() {
        $this->redirectToLogin();

        $idUsuario = $_SESSION['id_usuario'];
        $idPartida = $_POST['id_partida'] ?? null;
        $idPregunta = $_POST['id_pregunta'] ?? null;
        $motivo = trim($_POST['motivo'] ?? '');

        if (empty($idPregunta) || empty($motivo) || empty($idPartida)) {
            header("Location: " . BASE_URL . "ReportarPreguntaController/mostrarFormulario?id_partida=$idPartida&error=campos");
            exit();
        }

        $ok = $this->reportarPreguntaModel->guardarReporte($idUsuario, $idPartida, $idPregunta, $motivo);

        if (!$ok) {
            error_log("ReportarPreguntaController: fallo al guardar reporte. user=$idUsuario partida=$idPartida pregunta=$idPregunta");
            header("Location: " . BASE_URL . "ReportarPreguntaController/mostrarFormulario?id_partida=$idPartida&error=savefail");
            exit();
        }

        header("Location: " . BASE_URL . "/?success=reporte_enviado");
        exit();
    }
}
