<?php

class PartidaModel{

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function iniciarPartidaCategoria(){
        $stmt = $this->conexion->prepare("SELECT categoria FROM categoria_pregunta");
        $stmt->execute();
        $result = ($stmt->get_result());

        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row['categoria'];
        }

        return $categorias;
    }

    public function generarPreguntasPorCategoria($categoria){

        $stmt = $this->conexion->prepare("SELECT pregunta FROM pregunta WHERE categoria = ?");
        $stmt->bind_param("s", $categoria);
        $stmt->execute();
        $result = ($stmt->get_result());
        $preguntas = $result->fetch_all(MYSQLI_ASSOC);
        return $preguntas;
    }


}