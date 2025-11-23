<?php

class AdminModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerCantidadUsuarios()
{
    $sql = $this->conexion->prepare("SELECT COUNT(*) AS total FROM usuario");

    $sql->execute();
    $result = $sql->get_result();
    $resultado = $result->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}

    public function obtenerCantidadPartidas()
    {
        $sql = $this->conexion->prepare("SELECT COUNT(*) AS total FROM partida");

        $sql->execute();
        $result = $sql->get_result();
        $resultado = $result->fetch_all(MYSQLI_ASSOC);
        return $resultado;
    }

    public function obtenerCantidadPreguntasCreadas()
    {
        $sql = $this->conexion->prepare("SELECT COUNT(*) AS total FROM pregunta WHERE id_pregunta > 100");

        $sql->execute();
        $result = $sql->get_result();
        $resultado = $result->fetch_all(MYSQLI_ASSOC);
        return $resultado;
    }

}