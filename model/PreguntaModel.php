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

}