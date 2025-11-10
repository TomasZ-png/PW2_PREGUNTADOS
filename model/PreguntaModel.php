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
    $sql = "INSERT INTO pregunta_sugerida (id_usuario, pregunta, categoria) 
            VALUES ('$idUsuario', '$pregunta', '$categoria')";
    $this->conexion->query($sql);

    $idPreguntaSugerida = $this->conexion->insert_id;

    foreach ($respuestas as $index => $textoRespuesta) {
        $esCorrecta = ($index == $correcta) ? 1 : 0;
        $sqlResp = "INSERT INTO respuesta_sugerida (id_pregunta_sugerida, respuesta, es_correcta)
                    VALUES ('$idPreguntaSugerida', '$textoRespuesta', '$esCorrecta')";
        $this->conexion->query($sqlResp);
    }
}


}