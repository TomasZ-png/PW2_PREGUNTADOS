<?php

// Asegúrate de que las rutas son correctas
use helpers\CategoryColorHelper;

include_once(__DIR__."/../model/UsuarioModel.php");
include_once(__DIR__."/../model/PartidaModel.php");
include_once(__DIR__."/../config/config.php");
include_once (__DIR__ ."/../helpers/CategoryColorHelper.php");

class PartidaController
{
    private $model;
    private $usuarioModel;
    private $renderer;
    private $basePath = BASE_URL;
    private $conexion;
    private $preguntaModel;

    // CONSTRUCTOR: Asegúrate de que tu ConfigFactory te inyecte estos 3:
    public function __construct($partidaModel, $renderer, $conexion)
    {
        $this->model = $partidaModel;
        $this->renderer = $renderer;
        $this->conexion = $conexion;
        // Asumiendo que UsuarioModel ya está incluido o disponible
        $this->usuarioModel = new UsuarioModel($this->conexion);
        $this->preguntaModel = new PreguntaModel($this->conexion);
    }
    
    public function mostrarHome()
    {
        if (!isset($_SESSION['id_usuario'])) {
            $this->redirectToLogin();
        } else {
             // Inicia la partida si no hay una activa (o va al login si no hay sesión)
            $this->redirectToRoute('PartidaController', 'iniciar');
        }
    }

    public function iniciar(){
//       $this->redirectToHome();

        // 🚫 Bloquear si el usuario es editor
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'EDITOR') {
            $_SESSION['feedback'] = "No tienes permiso para jugar partidas (rol: Editor).";
            $this->redirectToRoute('HomeController', 'mostrarHome');
            return;
        }

        // Previene iniciar una partida si ya hay una activa
        if (isset($_SESSION['partidaId'])) {
            $this->redirectToRoute('PartidaController', 'jugar');
            return;
        }

        $idJugador = $_SESSION['id_usuario'];

        $partidaId = $this->model->iniciarPartida($idJugador);

        $_SESSION['partidaId'] = $partidaId;
        
        $this->redirectToRoute('PartidaController', 'mostrarRuleta');
    }

    public function mostrarRuleta(){

        $categoriaGanada = $_SESSION['categoriaGanada'] ?? null;
        unset($_SESSION['categoriaGanada']);

        $colorCategoria = null;
        if ($categoriaGanada !== null) {
            $colorCategoria = \helpers\CategoryColorHelper::getColorFor($categoriaGanada);
        }

        $categoriasBrutas = $this->preguntaModel->obtenerCategoriasParaRuleta();

        $colores = ["#fc8448", "#73bd58", "#ba87d1", "#fb4551", "#3d9bd0"];
        $categorias = [];

        foreach ($categoriasBrutas as $i => $cat) {
            $categorias[] = [
                "nombre" => $cat,
                "color"  => \helpers\CategoryColorHelper::getColorFor($cat)
            ];
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoria'])){
            error_log('mostrarRuleta: categoria recibida -> ' . $_POST['categoria']);
            $_SESSION['categoria'] = $_POST['categoria'];
            $_SESSION['numeroDePreguntasPorCategoria'] = 0;
            $this->redirectToRoute('PartidaController', 'jugar');
            return;
        }

        $this->renderer->renderWoHaF("ruleta", [
            "BASE_URL" => BASE_URL,
            "categoriaGanada" => $categoriaGanada,
            "colorCategoria" => $colorCategoria,
            "categorias" => json_encode($categorias)
        ]);
    }

    // Muestra la pregunta actual o finaliza el juego
    public function jugar(){
        $this->redirectToHome();

        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'EDITOR') {
            $_SESSION['feedback'] = "No tienes permiso para jugar partidas (rol: Editor).";
            $this->redirectToRoute('HomeController', 'mostrarHome');
            return;
        }

        if (!isset($_SESSION['partidaId'])) {
            $this->redirectToRoute('PartidaController', 'iniciar');
            return;
        }

        if($_SESSION['numeroDePreguntasPorCategoria'] == 5){
            $_SESSION['categoriaGanada'] = $_SESSION['categoria'];
            $_SESSION['numeroDePreguntasPorCategoria'] = 0;
            $this->redirectToRoute('PartidaController', 'mostrarRuleta');
        }

        $categoria = $_SESSION['categoria'] ?? null;

        if($categoria === null){
            $this->redirectToRoute('PartidaController', 'mostrarRuleta');
            return;
        }

        $_SESSION['tiempoPartidaIniciada'] = time();
        
        $partidaId = $_SESSION['partidaId'];
        $estado = $this->model->getEstadoPartida($partidaId);
        
        // El juego termina si el estado es PERDIDA
        if ($estado['estado_partida'] === 'PERDIDA' || $estado['estado_partida'] === 'PERDIDA_POR_TIEMPO' || $estado['estado_partida'] === 'PARDIDA_POR_RECARGA') {
            $this->finalizar($estado);
            return;
        }

        // Obtener IDs de preguntas jugadas (la cadena de IDs)
        $preguntasJugadas = $estado['preguntas_jugadas'];
        
        // Obtener la siguiente pregunta que no haya sido jugada

        $idJugador = $_SESSION['id_usuario'];

        if(!$idJugador){
            $this->redirectToLogin();
        }
        $usuario = $this->usuarioModel->getById($idJugador);

        $pregunta = $this->model->obtenerPreguntaAleatoria($preguntasJugadas, $_SESSION['preguntaID'], $categoria, $usuario['nivel_usuario']);

        if($pregunta === null){
            $this->finalizar(['estado_partida' => 'TERMINADO_POR_RECARGA', 'puntaje_final' => $estado['puntaje_final']]);
            $this->model->verificarRespuesta($partidaId, 0, true, true);
            return;
        }

        $_SESSION['preguntaID'] = $pregunta['id_pregunta'];

        if (!$pregunta) {
            // Caso: El jugador ha respondido TODAS las preguntas de la BD (Fin por completar)
            $this->finalizar(['estado_partida' => 'TERMINADO', 'puntaje_final' => $estado['puntaje_final']]);
            return;
        }

        $colorCategoria = CategoryColorHelper::getColorFor($pregunta['categoria']);

        $datos = [
            'pregunta' => $pregunta['pregunta'],
            'dificultad' => $pregunta['dificultad'],
            'categoria' => $pregunta['categoria'],
            'colorCategoria' => $colorCategoria,
            'puntaje' => $estado['puntaje_final'],
            'respuestas' => $pregunta['respuestas'],
            'feedback' => $_SESSION['feedback'] ?? null,
            'basePath' => $this->basePath
        ];

        // Limpiar feedback después de mostrarlo
        unset($_SESSION['feedback']);

        $this->renderer->renderWoHaF("jugarPartida", $datos, [
            "BASE_URL" => BASE_URL]);
        $_SESSION['numeroDePreguntasPorCategoria']++;
    }
    
    // Procesa la respuesta del jugador
    public function responder()
    {
        if (!isset($_SESSION['partidaId']) || !isset($_POST['respuestaId'])) {
            $this->redirectToRoute('PartidaController', 'jugar');
            return;
        }

        $tiempoTardado = $_SESSION['tiempoPartidaIniciada'];
        $tiempoAhora = time();

        $partidaId = $_SESSION['partidaId'];
        $idRespuesta = $_POST['respuestaId'];
        $tiempoFinalizado = false;

        if(($tiempoAhora - $tiempoTardado) > 10){
            $tiempoFinalizado = true;
        }

        $esCorrecta = $this->model->verificarRespuesta($partidaId, $idRespuesta, $tiempoFinalizado);

        // Obtener el estado después de la verificación para el feedback
        $estado = $this->model->getEstadoPartida($partidaId);

        if ($esCorrecta && !$tiempoFinalizado) {
            $_SESSION['feedback'] = "¡Respuesta Correcta! Sigue sumando puntos.";
            unset($_SESSION['preguntaID']);
            unset($_SESSION['tiempoPartidaIniciada']);
            // Si es correcta, el modelo la marcó, se redirige a jugar para la siguiente
        } elseif($tiempoFinalizado) {
            $_SESSION['feedback'] = "¡Tiempo agotado! Fin de la partida. Puntaje obtenido: " . $estado['puntaje_final'] . " puntos.";
            unset($_SESSION['preguntaID']);

            $estado = $this->model->getEstadoPartida($partidaId);
            $this->finalizar($estado);
            return;
        } else {
            // Si es incorrecta, el modelo la marcó como 'PERDIDA' y guardó el puntaje.
            $_SESSION['feedback'] = "¡Respuesta Incorrecta! Fin de la partida. Puntaje obtenido: " . $estado['puntaje_final'] . " puntos.";
            unset($_SESSION['preguntaID']);
        }

        $this->redirectToRoute('PartidaController', 'jugar');
    }

    private function finalizar($estado)
    {
        $puntajeFinal = $estado['puntaje_final'];
        $id_usuario = $_SESSION['id_usuario'];

        // SUMAMOS PUNTOS
        $this->usuarioModel->sumarPuntosUsuario($id_usuario, $puntajeFinal);

        // NUEVO: obtener respuesta correcta si perdió por falla
        $respuestaCorrecta = null;
        if ($estado['estado_partida'] === 'PERDIDA') {
            $respuestaCorrecta = $this->model->obtenerRespuestaCorrecta($_SESSION['partidaId']);
        }

        if ($estado['estado_partida'] === 'PERDIDA') {
            $mensaje = "¡Juego Terminado! Fallaste una pregunta.";
        } elseif ($estado['estado_partida'] === 'TERMINADO_POR_RECARGA') {
            $mensaje = "Perdiste la partida por recargar la página o salir del juego.";
        } elseif ($estado['estado_partida'] === 'PERDIDA_POR_TIEMPO') {
            $mensaje = "Perdiste!, el tiempo se agotó.";
        } else {
            $mensaje = "¡Increíble! Has respondido todas las preguntas de la base de datos.";
        }

        $puntajeMaximo = $this->usuarioModel->getPuntajeMaximo($id_usuario);

        $datos = [
            'mensaje' => $mensaje,
            'puntaje' => $puntajeFinal,
            'puntajeMaximo' => $puntajeMaximo,
            'respuestaCorrecta' => $respuestaCorrecta,   // ⬅⬅⬅ AGREGADO
            'id_partida' => $estado['id_partida'] ?? ($_SESSION['partidaId'] ?? null),
            'basePath' => $this->basePath
        ];

        $this->model->actualizarHistorialPartida($datos, $id_usuario, $estado['estado_partida']);

        $this->usuarioModel->actualizarNivelDeUsuario($id_usuario);

        // limpiar sesión
        unset($_SESSION['partidaId']);
        unset($_SESSION['feedback']);
        unset($_SESSION['preguntaID']);
        unset($_SESSION['tiempoPartidaIniciada']);

        $this->renderer->renderWoHaF("resultadoPartida", $datos, [
            "BASE_URL" => BASE_URL
        ]);
    }

    // --- Utilidades ---

    private function redirectToLogin(){
        if(!isset($_SESSION['id_usuario'])){
            $this->redirectToRoute('LoginController', 'login');
        }
    }

    private function redirectToHome(){
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'EDITOR') {
            $_SESSION['feedback'] = "No tienes permiso para jugar partidas (rol: Editor).";
            $this->redirectToRoute('HomeController', 'mostrarHome');
        }
    }

    private function redirectToRoute($controller, $method){
        header("Location: " . $this->basePath . "$controller/$method");
        exit();
    }
}