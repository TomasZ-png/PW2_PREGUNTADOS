<?php

class PartidaModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Inicia una nueva partida
    public function iniciarPartida($idJugador)
    {
        $sql = "INSERT INTO partida (id_jugador, estado_partida, puntaje_final, fecha_creacion, categorias_ganadas) 
                VALUES ($idJugador, 'ACTIVA', 0, CURDATE(), '')";
        
        $this->conexion->query($sql);
        // Asumiendo que MyDatabase tiene un método para obtener el último ID
        return $this->conexion->getConexion()->insert_id; 
    }

    // Obtiene una pregunta aleatoria de una categoría AÚN NO GANADA
    public function obtenerPreguntaAleatoria($categoriasGanadas)
    {
        // 1. Obtener una categoría que no esté en la lista $categoriasGanadas
        $categoriasExcluir = empty($categoriasGanadas) ? '""' : '"' . implode('","', $categoriasGanadas) . '"';
        
        $sqlCategoria = "SELECT categoria FROM categoria_pregunta 
                         WHERE categoria NOT IN ($categoriasExcluir)
                         ORDER BY RAND() LIMIT 1";
        
        $resultadoCategoria = $this->conexion->query($sqlCategoria);

        if (empty($resultadoCategoria)) {
            return null; // Todas las categorías han sido ganadas
        }

        $categoriaSeleccionada = $resultadoCategoria[0]['categoria'];

        // 2. Obtener una Pregunta aleatoria de esa categoría
        $sqlPregunta = "SELECT id_pregunta, pregunta, categoria FROM pregunta 
                        WHERE categoria = '$categoriaSeleccionada'
                        ORDER BY RAND() LIMIT 1";
        
        $pregunta = $this->conexion->query($sqlPregunta);

        if (empty($pregunta)) {
            return null; // No hay preguntas en esa categoría
        }

        $idPregunta = $pregunta[0]['id_pregunta'];

        // 3. Obtener las 4 respuestas para esa pregunta
        $sqlRespuestas = "SELECT id_respuesta, respuesta, es_correcta FROM respuesta 
                          WHERE id_pregunta = $idPregunta ORDER BY RAND()";
        $respuestas = $this->conexion->query($sqlRespuestas);

        $pregunta[0]['respuestas'] = $respuestas;
        
        return $pregunta[0];
    }
    
    // Verifica si la respuesta es correcta y actualiza la partida
    public function verificarRespuesta($idPartida, $idRespuesta)
    {
        // 1. Verificar si la respuesta seleccionada es la correcta
        $sqlVerif = "SELECT r.es_correcta, p.categoria
                     FROM respuesta r JOIN pregunta p ON r.id_pregunta = p.id_pregunta
                     WHERE r.id_respuesta = $idRespuesta";
                     
        $resultado = $this->conexion->query($sqlVerif);
        
        if (empty($resultado)) { return false; }

        $esCorrecta = $resultado[0]['es_correcta'] == 1;
        $categoriaPregunta = $resultado[0]['categoria'];

        // 2. Actualizar Partida
        if ($esCorrecta) {
            // Se asume 5 puntos por pregunta, basado en tus inserciones SQL
            $sqlAct = "UPDATE partida SET puntaje_final = puntaje_final + 5, 
                       categorias_ganadas = CONCAT(categorias_ganadas, '$categoriaPregunta,') 
                       WHERE id_partida = $idPartida";
            $this->conexion->query($sqlAct);
        } else {
            // FIN DE PARTIDA: Actualizar estado y puntaje final
            $sqlAct = "UPDATE partida SET estado_partida = 'PERDIDA', fecha_fin = NOW()
                       WHERE id_partida = $idPartida";
            $this->conexion->query($sqlAct);
        }

        return $esCorrecta;
    }
    
    // Obtiene el estado actual de la partida
    public function getEstadoPartida($idPartida)
    {
        $sql = "SELECT estado_partida, categorias_ganadas, puntaje_final 
                FROM partida WHERE id_partida = $idPartida";
        $resultado = $this->conexion->query($sql);
        return $resultado[0] ?? null;
    }
}