<?php

class HomeModel
{

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function obtenerUsuario($id_usuario){
        $stmt = $this->conexion->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}