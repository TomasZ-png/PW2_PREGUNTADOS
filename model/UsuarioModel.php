<?php

class UsuarioModel
{

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function listarUsuarios(){
        $stmt = $this->conexion->prepare("SELECT nombre_completo, foto_perfil, puntaje_global
                                          FROM usuario 
                                          WHERE rol = 'USER' 
                                          ORDER BY puntaje_global DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
        return $usuarios;
    }

    public function sumarPuntosUsuario($id_usuario, $puntaje_final){
        $stmt = $this->conexion->prepare("UPDATE usuario 
                                           SET puntaje_global = COALESCE(puntaje_global, 0) + ? 
                                           WHERE id_usuario = ?");
        $stmt->bind_param("ii", $puntaje_final, $id_usuario);
        $stmt->execute();
    }
}