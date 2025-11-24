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

        $dificultad = $this->calcularDificultad($pregunta['cant_acertadas'], $pregunta['cant_erroneas'], $preguntaInteraccion);

//        if($preguntaInteraccion <= 5){
//            if($pregunta["cant_acertadas"] > $pregunta["cant_erroneas"]){
//                $query = "UPDATE pregunta SET dificultad = 'FACIL' WHERE id_pregunta = $idPregunta";
//            } else {
//                $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//            }
//        } elseif ($preguntaInteraccion >= 6 && $preguntaInteraccion <= 9) {
//            if($pregunta["cant_acertadas"] > $pregunta["cant_erroneas"]){
//                if(($pregunta["cant_acertadas"] - $pregunta["cant_erroneas"]) >= 5 ){
//                    $query = "UPDATE pregunta SET dificultad = 'FACIL' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//                }
//            } else {
//                if(($pregunta["cant_erroneas"] - $pregunta["cant_acertadas"]) >= 10 ){
//                    $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//                }
//            }
//        } elseif ($preguntaInteraccion >= 10 && $preguntaInteraccion <= 20) {
//            if($pregunta["cant_acertadas"] > $pregunta["cant_erroneas"]){
//                if(($pregunta["cant_acertadas"] - $pregunta["cant_erroneas"]) >= 5 ){
//                    $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
//                }
//            } else {
//                if(($pregunta["cant_erroneas"] - $pregunta["cant_acertadas"]) >= 10 ){
//                    $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//                }
//            }
//        } else {
//            if($pregunta["cant_acertadas"] > $pregunta["cant_erroneas"]){
//                if(($pregunta["cant_acertadas"] - $pregunta["cant_erroneas"]) >= 5 ){
//                    $query = "UPDATE pregunta SET dificultad = 'MEDIO' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
//                }
//            } else {
//                if(($pregunta["cant_erroneas"] - $pregunta["cant_acertadas"]) >= 20 ){
//                    $query = "UPDATE pregunta SET dificultad = 'IMPOSIBLE' WHERE id_pregunta = $idPregunta";
//                } else {
//                    $query = "UPDATE pregunta SET dificultad = 'DIFICIL' WHERE id_pregunta = $idPregunta";
//                }
//            }
//        }
        $query = "UPDATE pregunta SET dificultad = '$dificultad' WHERE id_pregunta = $idPregunta";

        $this->conexion->query($query);
    }



    public function calcularDificultad($aciertos, $errores, $interacciones){
        $diferencia = abs($aciertos - $errores);
        $masAciertos = $aciertos > $errores;

        if($interacciones <= 5){
            return $masAciertos? 'NUEVA' : 'FACIL';
        } elseIf($interacciones >= 6 && $interacciones <= 9){
            if($masAciertos){
                return $diferencia >= 5 ? 'FACIL' : 'MEDIO';
            } else {
                return $diferencia >= 5 ? 'DIFICIL' : 'MEDIO';
            }
        } elseif($interacciones >= 10 && $interacciones <= 20){
            if($masAciertos){
                return $diferencia >= 5 ? 'MEDIO' : 'DIFICIL';
            } else {
                return $diferencia >= 10 ? 'DIFICIL' : 'MEDIO';
            }
        } else {
            if($masAciertos){
                return $diferencia >= 5 ? 'MEDIO' : 'DIFICIL';
            } else {
                return $diferencia >= 20 ? 'IMPOSIBLE' : 'DIFICIL';
            }
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

    public function obtenerCategorias() {
        $sql = "SELECT id_categoria, categoria FROM categoria_pregunta";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando consulta de categorías: " . $this->conexion->error);
            return [];
        }

        $stmt->execute();

        $result = $stmt->get_result();
        $categorias = [];

        while ($fila = $result->fetch_assoc()) {
            $categorias[] = $fila;
        }

        $stmt->close();

        return $categorias;
    }

    public function obtenerCantidadDePreguntasDeCategoria($categoria) {
        $sql = "SELECT COUNT(id_pregunta) AS total FROM pregunta WHERE categoria = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $categoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function obtenerCategoriasParaRuleta(){
        $sql = "SELECT categoria FROM categoria_pregunta";
        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando consulta de categorías: " . $this->conexion->error);
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $categorias = [];

        while ($fila = $result->fetch_assoc()) {
            $categorias[] = $fila['categoria'];
        }

        $categoriasADevolver = [];

        foreach ($categorias as $categoria) {
            $preguntasXCategoria = $this->obtenerCantidadDePreguntasDeCategoria($categoria);

            if($preguntasXCategoria >= 5){
                $categoriasADevolver[] = $categoria;
            }
        }

        $stmt->close();
        return $categoriasADevolver;
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