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
    $sql = $this->conexion->prepare("SELECT COUNT(*) AS total FROM usuario"); // revisa que tu tabla se llame "usuario"

    $sql->execute();
    $result = $sql->get_result();
    $resultado = $result->fetch_all(MYSQLI_ASSOC);
    return $resultado;
}



}