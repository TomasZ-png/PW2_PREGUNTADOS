<?php

class PerfilModel {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT id_usuario, nombre_completo, correo, anio_nacimiento, sexo, foto_perfil, puntaje_maximo_obtenido, nivel_usuario
                FROM usuario
                WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function obtenerDireccionByIdUsuario($id_usuario) {
        $sql = "SELECT id_usuario, longitud, latitud, ciudad, pais FROM direccion_usuario WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }
}
