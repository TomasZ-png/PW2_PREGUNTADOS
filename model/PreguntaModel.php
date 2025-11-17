<?php

class PreguntaModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function actualizarDificultadPregunta($idPregunta){
        $preguntaSql = "SELECT * FROM pregunta WHERE id_pregunta = $idPregunta";
        $pregunta = $this->conexion->query($preguntaSql);

        if (empty($pregunta)) {
            return;
        }

        $pregunta = $pregunta[0];

        $preguntaInteraccion = $pregunta['cant_acertadas'] + $pregunta['cant_erroneas'];

        if($preguntaInteraccion >= 5){
            if($pregunta["cant_acertadas"] > $pregunta["cant_erroneas"]){
                $query = "UPDATE pregunta SET dificultad = 'FACIL' WHERE id_pregunta = $idPregunta";
            } else {
                $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
            }
            $this->conexion->query($query);
        }
    }


    //guarrdar la pregunta sugerida
     public function guardarSugerencia($idUsuario, $pregunta, $categoria, $respuestas, $correcta) {
        $idPreguntaSugerida = null;
    
        $sql = "INSERT INTO pregunta_sugerida (id_usuario, pregunta, categoria) 
                VALUES (?, ?, ?)";
        
        $stmt_pregunta = $this->conexion->prepare($sql);

        if (!$stmt_pregunta) {
            error_log("Error preparando sentencia de pregunta: " . $this->conexion->error);
            return false;
        }

        $stmt_pregunta->bind_param("iss", $idUsuario, $pregunta, $categoria);

        if (!$stmt_pregunta->execute()) {
            error_log("Error ejecutando inserción de pregunta: " . $stmt_pregunta->error);
            $stmt_pregunta->close();
            return false;
        }

        $idPreguntaSugerida = $stmt_pregunta->insert_id;
        $stmt_pregunta->close();

        if ($idPreguntaSugerida === 0 || $idPreguntaSugerida === null) {
             error_log("Error: El ID de la pregunta no fue generado. Abortando inserción de respuestas.");
             return false;
        }

        $sqlResp = "INSERT INTO respuesta_sugerida (id_pregunta_sugerida, respuesta, es_correcta)
                    VALUES (?, ?, ?)";
        
        $stmt_respuesta = $this->conexion->prepare($sqlResp);

        if (!$stmt_respuesta) {
            error_log("Error preparando sentencia de respuesta: " . $this->conexion->error);
            return false;
        }

        foreach ($respuestas as $index => $textoRespuesta) {
            $esCorrecta = ($index == $correcta) ? 1 : 0;
            
            $stmt_respuesta->bind_param("isi", $idPreguntaSugerida, $textoRespuesta, $esCorrecta);
            
            if (!$stmt_respuesta->execute()) {
                error_log("Error ejecutando inserción de respuesta sugerida para ID " . $idPreguntaSugerida . ": " . $stmt_respuesta->error);
            }
        }

        $stmt_respuesta->close();
        return true;
    }

    public function obtenerPreguntasDeFacilesADificiles(){
        $stmt = $this->conexion->prepare("SELECT p.pregunta, SUM(p.cant_erroneas) AS erroneas, SUM(p.cant_acertadas) AS acertadas
                                          FROM pregunta p
                                          WHERE p.cant_acertadas IS NOT NULL OR p.cant_erroneas IS NOT NULL
                                          GROUP BY p.pregunta
                                          HAVING erroneas > acertadas
                                          ORDER BY (erroneas) DESC
                                          LIMIT 10;");

        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

}