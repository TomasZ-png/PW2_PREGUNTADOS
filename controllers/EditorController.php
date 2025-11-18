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

    // Traer categorías dinámicas desde la BD
    $categorias = $this->editorModel->getCategorias();

    // Marcar cual está seleccionada
    foreach ($categorias as &$cat) {
        $cat["seleccionada"] = ($cat["categoria"] === $pregunta["categoria"]);
    }

    $data = [
        "pregunta"   => $pregunta,
        "respuestas" => $pregunta['respuestas'],
        "categorias" => $categorias,
        "BASE_URL"   => BASE_URL
    ];

    $this->renderer->render("editarPregunta", $data);
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


    public function eliminarPregunta()
{
    $this->verificarEditor();

    $id = $_GET['id'] ?? null;

    if (!$id) {
        die("ID inválido");
    }

    $resultado = $this->editorModel->eliminarPregunta($id);

    if ($resultado) {
        header("Location: " . BASE_URL . "EditorController/home?ok=eliminada");
        exit();
    } else {
        die("Error al eliminar la pregunta");
    }
}


public function crearCategoria()
{
    $this->verificarEditor();

    if ($_SESSION['rol'] !== 'EDITOR') {
        die("No autorizado");
    }

    $categoria = trim($_POST['categoria'] ?? '');

    if ($categoria === '') {
        header("Location: ".BASE_URL."EditorController/home?error_categoria=1");
        exit();
    }

    if ($this->editorModel->categoriaExiste($categoria)) {
        header("Location: ".BASE_URL."EditorController/home?error_categoria=1");
        exit();
    }

    $this->editorModel->crearCategoria($categoria);

    header("Location: ".BASE_URL."EditorController/home?success_categoria=1");
    exit();
}


public function crearPregunta()
{
    $this->verificarEditor();

    // traer categorias desde el model
    $categorias = $this->editorModel->getCategorias();

    // generar 4 campos para respuestas
    $respuestas = [
        ["n" => 1, "index" => 0],
        ["n" => 2, "index" => 1],
        ["n" => 3, "index" => 2],
        ["n" => 4, "index" => 3],
    ];

    $data = [
        "categorias" => $categorias,
        "respuestas" => $respuestas,
        "BASE_URL" => BASE_URL
    ];

    $this->renderer->render("crearPregunta", $data);
}



public function guardarPregunta()
{
    $this->verificarEditor();

    $pregunta = trim($_POST["pregunta"]);
    $categoria = $_POST["categoria"];
    $puntaje = intval($_POST["puntaje"]);
    $respuestas = $_POST["respuestas"] ?? [];
    $correcta = intval($_POST["correcta"]);

    if ($pregunta === "" || count($respuestas) !== 4) {
        header("Location: ".BASE_URL."EditorController/home?error_crearPregunta=1");
        exit();
    }

    $this->editorModel->crearPregunta($pregunta, $categoria, $puntaje, $respuestas, $correcta);

    header("Location: ".BASE_URL."EditorController/home?ok=preguntaCreada");
    exit();
}



public function verPreguntaReportada()
{
    $this->verificarEditor();

    $idReporte = $_GET["id"] ?? null;

    if (!$idReporte) {
        die("ID de reporte inválido");
    }

    // 1. Marcar reporte como revisado
    $this->editorModel->marcarReporteRevisado($idReporte);

    // 2. Obtener id de la pregunta
    $idPregunta = $this->editorModel->obtenerPreguntaDesdeReporte($idReporte);

    if (!$idPregunta) {
        die("No se encontró la pregunta asociada al reporte");
    }

    // 3. Redirigir a editarPregunta
    header("Location: ".BASE_URL."EditorController/editarPregunta?id=".$idPregunta);
    exit();
}



}
