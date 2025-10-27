<?php

class UsuarioModel
{

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function sumarPuntosUsuario($id_usuario, $puntaje_final){
        $stmt = $this->conexion->prepare("UPDATE usuario 
                                           SET puntaje_global = COALESCE(puntaje_global, 0) + ? 
                                           WHERE id_usuario = ?");
        $stmt->bind_param("ii", $puntaje_final, $id_usuario);
        $stmt->execute();
    }
}