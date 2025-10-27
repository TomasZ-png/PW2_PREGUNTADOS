<?php
// Asegúrate de incluir el modelo en el ConfigFactory, no aquí.

class PartidaController
{
    private $model;
    private $renderer;
    private $basePath = '/PROYECTO_PREGUNTADOS/'; // Usa tu carpeta real

    public function __construct($partidaModel, $renderer)
    {
        $this->model = $partidaModel;
        $this->renderer = $renderer;
    }
    
    // Redirige al inicio si no hay partida, o a jugar si ya hay una.
    public function base()
    {
        if (!isset($_SESSION['id_usuario'])) {
             // Redirigir al login si no hay usuario logueado
             $this->redirectToRoute('LoginController', 'login');
        } else if (!isset($_SESSION['partidaId'])) {
            $this->iniciar();
        } else {
            $this->jugar();
        }
    }

    // Crea un nuevo registro de partida
    public function iniciar()
    {
        // Asumiendo que el ID del usuario logueado es el ID del jugador
        $idJugador = $_SESSION['id_usuario'] ?? 1; 
        
        $partidaId = $this->model->iniciarPartida($idJugador);

        $_SESSION['partidaId'] = $partidaId;
        
        $this->redirectToRoute('PartidaController', 'jugar');
    }

    // Muestra la pregunta actual o finaliza el juego
    public function jugar()
    {
        if (!isset($_SESSION['partidaId'])) {
            $this->redirectToRoute('PartidaController', 'iniciar');
        }
        
        $partidaId = $_SESSION['partidaId'];
        $estado = $this->model->getEstadoPartida($partidaId);
        
        if ($estado['estado_partida'] === 'PERDIDA') {
            $this->finalizar($estado);
            return;
        }

        $categoriasGanadas = array_filter(explode(',', $estado['categorias_ganadas']));
        
        // La ruleta: obtener la siguiente pregunta
        $pregunta = $this->model->obtenerPreguntaAleatoria($categoriasGanadas);
        
        if (!$pregunta) {
            // El jugador ganó todas las categorías disponibles
            $this->finalizar(['estado_partida' => 'GANADA', 'puntaje_final' => $estado['puntaje_final']]);
            return;
        }

        $datos = [
            'pregunta' => $pregunta['pregunta'],
            'categoria' => $pregunta['categoria'],
            'puntaje' => $estado['puntaje_final'],
            'respuestas' => $pregunta['respuestas'],
            'feedback' => $_SESSION['feedback'] ?? null // Para mostrar el resultado de la ronda anterior
        ];
        
        // Limpiar feedback después de mostrarlo
        unset($_SESSION['feedback']);

        $this->renderer->render("jugarPartida", $datos);
    }
    
    // Procesa la respuesta del jugador
    public function responder()
    {
        if (!isset($_SESSION['partidaId']) || !isset($_POST['respuestaId'])) {
            $this->redirectToRoute('PartidaController', 'jugar');
        }
        
        $partidaId = $_SESSION['partidaId'];
        $idRespuesta = $_POST['respuestaId'];

        $esCorrecta = $this->model->verificarRespuesta($partidaId, $idRespuesta);

        if ($esCorrecta) {
            $_SESSION['feedback'] = "¡Respuesta Correcta! Has ganado la categoría.";
        } else {
            $_SESSION['feedback'] = "¡Respuesta Incorrecta! Fin de la partida.";
        }
        
        $this->redirectToRoute('PartidaController', 'jugar');
    }

    private function finalizar($estado)
    {
        $mensaje = $estado['estado_partida'] === 'PERDIDA' ? 
                   "¡Juego Terminado! Respuesta Incorrecta. Intenta de nuevo." : 
                   "¡Felicidades! ¡Has Ganado el Juego al completar todas las categorías!";
        
        $datos = [
            'mensaje' => $mensaje,
            'puntaje' => $estado['puntaje_final']
        ];
        
        // Limpiar la sesión de la partida actual
        unset($_SESSION['partidaId']);
        
        $this->renderer->render("resultadoPartida", $datos);
    }
    
    // --- Utilidades ---
    private function redirectToRoute($controller, $method)
    {
        header("Location: " . $this->basePath . "$controller/$method");
        exit();
    }
}