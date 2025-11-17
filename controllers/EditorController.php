<?php

include_once(__DIR__ . "/../model/EditorModel.php");

class EditorController {

    private $conexion;
    private $renderer;
    private $editorModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->editorModel = new EditorModel($conexion);
    }

    private function verificarEditor() {
        if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

        if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != "EDITOR") {
            header("Location: " . BASE_URL . "?error=no_editor");
            exit();
        }
    }

    public function home() {
    $this->verificarEditor();

    $preguntas = $this->editorModel->getPreguntasNormales();
    $sugeridas = $this->editorModel->getPreguntasSugeridas();
    $reportadas = $this->editorModel->getPreguntasReportadas();

    $data = [
        "usuario" => $_SESSION["nombre"] ?? "",
        "preguntas" => $preguntas,
        "sugeridas" => $sugeridas,
        "reportadas" => $reportadas,
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("editor", $data);
}




    public function listarSugeridas() {
        $this->verificarEditor();

        $sugeridas = $this->editorModel->getPreguntasSugeridas();

        $this->renderer->render("sugeridasVista", [
            'sugeridas' => $sugeridas
        ]);
    }

    public function aceptarSugerida() {
    $this->verificarEditor();

    $id = $_GET['id'] ?? null;

    if ($id) {
        $this->editorModel->aceptarSugerencia($id);
    }

    header("Location: " . BASE_URL . "EditorController/home?ok=sugeridaAceptada");
    exit();
}


    public function rechazarSugerida() {
    $this->verificarEditor();

    $id = $_GET['id'] ?? null;

    if ($id) {
        $this->editorModel->rechazarSugerencia($id);
    }

    header("Location: " . BASE_URL . "EditorController/home?ok=sugeridaRechazada");
    exit();
}




    public function listarReportadas() {
        $this->verificarEditor();

        $reportes = $this->editorModel->getPreguntasReportadas();

        $this->renderer->render("reportesVista", [
            'reportes' => $reportes
        ]);
    }

    public function aceptarReporte() {
        $this->verificarEditor();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->editorModel->aceptarReporte($id);
        }

        header("Location: " . BASE_URL . "EditorController/home?ok=reportadaAceptada");
        exit();
    }

    public function rechazarReporte() {
        $this->verificarEditor();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->editorModel->rechazarReporte($id);
        }

        header("Location: " . BASE_URL . "EditorController/home?ok=reportadaRechazada");
        exit();
    }
       
   public function editarPregunta() {
    $this->verificarEditor();

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo "ID no válido";
        exit();
    }

    $pregunta = $this->editorModel->getPreguntaCompleta($id);

    if (!$pregunta) {
        echo "La pregunta no existe";
        exit();
    }

    $categoria = $pregunta["categoria"];

    $pregunta["isCategoriaHISTORIA"]  = ($categoria === "HISTORIA");
    $pregunta["isCategoriaDEPORTE"]  = ($categoria === "DEPORTE");
    $pregunta["isCategoriaCIENCIA"]  = ($categoria === "CIENCIA");
    $pregunta["isCategoriaGEOGRAFIA"] = ($categoria === "GEOGRAFIA");
    $pregunta["isCategoriaARTE"]     = ($categoria === "ARTE");

    $this->renderer->render("editarPregunta", [
        'pregunta'   => $pregunta,
        'respuestas' => $pregunta['respuestas'],
        'BASE_URL'   => BASE_URL
    ]);
}


    public function guardarEdicion() {
        $this->verificarEditor();

        $idPregunta = $_POST['id_pregunta'];
        $texto = $_POST['pregunta'];
        $categoria = $_POST['categoria'];
        $puntaje = $_POST['puntaje'];
        $correcta = $_POST['correcta'];
        $respuestas = $_POST['respuestas'];

        $this->editorModel->guardarEdicion($idPregunta, $texto, $categoria, $puntaje, $respuestas, $correcta);

        header("Location: " . BASE_URL . "EditorController/home?ok=editado");
        exit();
    }
}
