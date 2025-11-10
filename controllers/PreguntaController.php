<?php

include_once(__DIR__ . "/../model/PreguntaModel.php");

class PreguntaController {

    private $conexion;
    private $renderer;
    private $preguntaModel;

    public function __construct($conexion, $renderer) {
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->preguntaModel = new PreguntaModel($conexion);
    }

    private function redirectToLogin() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "LoginController/login");
            exit();
        }
    }

    public function mostrarFormularioSugerir() {
        $this->redirectToLogin();
        $this->renderer->render("sugerirPregunta", ['respuestas' => [1, 2, 3, 4]]);
    }

   public function guardarSugerencia() {
    $this->redirectToLogin();

    $idUsuario = $_SESSION['id_usuario'];
    $pregunta = $_POST['pregunta'];
    $categoria = $_POST['categoria'];
    $respuestas = $_POST['respuestas'];
    $correcta = intval($_POST['correcta']);

    // Validaciones
    if (empty($pregunta) || empty($categoria) || count($respuestas) != 4) {
        // 👇 redirección correcta
        header("Location: " . BASE_URL . "PreguntaController/mostrarFormularioSugerir?error=campos");
        exit();
    }

    // Guardar sugerencia
    $this->preguntaModel->guardarSugerencia($idUsuario, $pregunta, $categoria, $respuestas, $correcta);

    // 👇 Redirige correctamente al HomeController
    header("Location: " . BASE_URL . "HomeController/mostrarHome?success=sugerencia");
    exit();
}

}
