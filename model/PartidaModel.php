<?php

class PartidaModel
{
    private $conexion;
    private $usuarioModel; // Usado para actualizar el puntaje máximo
    private $preguntaModel;

    // El constructor debe inyectar la conexión y crear el modelo de usuario.
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        // Asumiendo que UsuarioModel ya está incluido o disponible
        $this->usuarioModel = new UsuarioModel($this->conexion);
        $this->preguntaModel = new PreguntaModel($this->conexion);
    }

    // Inicia una nueva partida
    public function iniciarPartida($idJugador)
    {
        // Se inicializa puntaje_final en 0 y preguntas_jugadas vacío.
        $sql = "INSERT INTO partida (id_jugador, estado_partida, puntaje_final, fecha_creacion, preguntas_jugadas) 
                 VALUES ($idJugador, 'ACTIVA', 0, CURDATE(), '')";
        
        $this->conexion->query($sql);
        // Retorna el ID de la partida recién creada
        return $this->conexion->getConexion()->insert_id; 
    }

    // Obtiene una pregunta aleatoria que NO haya sido jugada en esta partida.
    public function obtenerPreguntaAleatoria($preguntasJugadas, $idPregunta)
    {

        if(isset($idPregunta)){
            return null;
        }

        // $preguntasJugadas viene de la BD como 'id1,id2,id3,...'
        $preguntasExcluir = empty($preguntasJugadas) ? '0' : rtrim($preguntasJugadas, ',');

        // 1. Obtener una Pregunta aleatoria que no esté en la lista $preguntasExcluir
        // Se selecciona la pregunta de cualquier categoría, aleatoriamente.
        $sqlPregunta = "SELECT p.id_pregunta, p.pregunta, p.categoria, p.puntaje 
                        FROM pregunta p
                        WHERE p.id_pregunta NOT IN ($preguntasExcluir)
                        ORDER BY RAND() LIMIT 1";
        
        $pregunta = $this->conexion->query($sqlPregunta);

        if (empty($pregunta)) {
            return null; // No hay más preguntas disponibles en toda la base de datos
        }

        $idPregunta = $pregunta[0]['id_pregunta'];

        // 2. Obtener las 4 respuestas para esa pregunta
        $sqlRespuestas = "SELECT id_respuesta, respuesta, es_correcta FROM respuesta 
                          WHERE id_pregunta = $idPregunta ORDER BY RAND()";
        $respuestas = $this->conexion->query($sqlRespuestas);

        $pregunta[0]['respuestas'] = $respuestas;
        
        return $pregunta[0];
    }
    
    // Verifica si la respuesta es correcta y actualiza la partida.
    public function verificarRespuesta($idPartida, $idRespuesta)
    {
        // 1. Obtener datos de la pregunta, puntaje y si es correcta.
        $sqlVerif = "SELECT r.es_correcta, p.id_pregunta, p.puntaje, pa.puntaje_final AS puntaje_actual, pa.id_jugador
                      FROM respuesta r 
                      JOIN pregunta p ON r.id_pregunta = p.id_pregunta
                      JOIN partida pa ON pa.id_partida = $idPartida
                      WHERE r.id_respuesta = $idRespuesta";

        $resultado = $this->conexion->query($sqlVerif);
        
        if (empty($resultado)) { return false; }

        $esCorrecta = $resultado[0]['es_correcta'] == 1;
        $idPregunta = $resultado[0]['id_pregunta'];
        $puntajePregunta = (int) $resultado[0]['puntaje']; // <<< CORRECCIÓN: CAST A INT
        $puntajeActual = (int) $resultado[0]['puntaje_actual']; // <<< CORRECCIÓN: CAST A INT
        $idJugador = $resultado[0]['id_jugador'];


        // 2. Actualizar Partida
        if ($esCorrecta) {
            // Correcta: Sumar puntos y registrar la pregunta jugada
            $sqlAct = "UPDATE partida 
                        SET puntaje_final = puntaje_final + $puntajePregunta, 
                            preguntas_jugadas = CONCAT(preguntas_jugadas, '$idPregunta,') 
                        WHERE id_partida = $idPartida";
            $this->conexion->query($sqlAct);

            // actualizamos la cantidad de veces acertadas
            $actualizarCantidadContestadas = "UPDATE pregunta
                                              SET cant_acertadas = COALESCE(cant_acertadas, 0) + 1
                                              WHERE id_pregunta = $idPregunta";
            $this->conexion->query($actualizarCantidadContestadas);

            //actualizamos la dificultad de la pregunta
            $this->preguntaModel->actualizarDificultadPregunta($idPregunta);
        } else {
            // Incorrecta: FIN DE PARTIDA.

            // actualizamos la cantidad de veces acertadas
            $actualizarCantidadErroneas = "UPDATE pregunta
                                              SET cant_erroneas = COALESCE(cant_acertadas, 0) + 1
                                              WHERE id_pregunta = $idPregunta";
            $this->conexion->query($actualizarCantidadErroneas);
            $this->preguntaModel->actualizarDificultadPregunta($idPregunta);


            $puntajeFinalObtenido = $puntajeActual; // El puntaje actual es el final (no suma la fallida)

            $sqlAct = "UPDATE partida SET estado_partida = 'PERDIDA', fecha_fin = NOW(), puntaje_final = $puntajeFinalObtenido
                        WHERE id_partida = $idPartida";
            $this->conexion->query($sqlAct);

            // Llama al método del UsuarioModel para verificar el puntaje máximo
            $this->usuarioModel->actualizarPuntajeMaximo($idJugador, $puntajeFinalObtenido);
        }

        return $esCorrecta;
    }

    // Obtiene el estado actual de la partida (MODIFICADO para lógica infinita)
    public function getEstadoPartida($idPartida)
    {
        $sql = "SELECT estado_partida, preguntas_jugadas, puntaje_final, id_jugador 
                FROM partida WHERE id_partida = $idPartida";
        $resultado = $this->conexion->query($sql);
        return $resultado[0] ?? null;
    }
}