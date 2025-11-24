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

    public function obtenerCantidadPreguntasTotales()
    {
        $sql = $this->conexion->prepare("SELECT COUNT(*) AS total FROM pregunta");

        $sql->execute();
        $result = $sql->get_result();
        $resultado = $result->fetch_all(MYSQLI_ASSOC);
        return $resultado;
    }

    public function obtenerCantidadUsuariosPorEdad()
    {
        $sql = $this->conexion->prepare("
        SELECT 
            SUM(CASE WHEN (YEAR(CURDATE()) - anio_nacimiento) < 18 THEN 1 ELSE 0 END) AS menores,
            SUM(CASE WHEN (YEAR(CURDATE()) - anio_nacimiento) BETWEEN 18 AND 64 THEN 1 ELSE 0 END) AS medios,
            SUM(CASE WHEN (YEAR(CURDATE()) - anio_nacimiento) >= 65 THEN 1 ELSE 0 END) AS jubilados
        FROM usuario
    ");

        $sql->execute();
        $result = $sql->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerJugadoresPorPais()
    {
        $sql = $this->conexion->prepare("
        SELECT pais, COUNT(*) AS cantidad
        FROM direccion_usuario
        GROUP BY pais
        ORDER BY cantidad DESC
    ");

        $sql->execute();
        $result = $sql->get_result();

        // Necesitamos devolver un array con el formato compatible con Google Charts
        $data = [];
        $data[] = ["País", "Cantidad"]; // ENCABEZADOS

        while ($row = $result->fetch_assoc()) {
            $data[] = [$row["pais"], (int)$row["cantidad"]];
        }

        return $data;
    }

    public function obtenerUsuariosNuevos($filtro = 'mes')
    {
        $where = "";
        $group = "";
        $select = "";
        $order = "";

        switch ($filtro) {

            case 'dia':
                $where = "WHERE DATE(fecha_creacion) = CURDATE()";
                $select = "DATE(fecha_creacion) AS periodo";
                $group = "GROUP BY DATE(fecha_creacion)";
                $order = "ORDER BY periodo ASC";
                break;

            case 'semana':
                $where = "WHERE YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
                $select = "DATE(fecha_creacion) AS periodo";
                $group = "GROUP BY DATE(fecha_creacion)";
                $order = "ORDER BY periodo ASC";
                break;

            case 'mes':
                $where = "WHERE YEAR(fecha_creacion) = YEAR(CURDATE())
                  AND MONTH(fecha_creacion) = MONTH(CURDATE())";
                $select = "DATE(fecha_creacion) AS periodo";
                $group = "GROUP BY DATE(fecha_creacion)";
                $order = "ORDER BY periodo ASC";
                break;

            case 'anio':
                // ⭐ TODOS LOS MESES DE TODOS LOS AÑOS
                $select = "DATE_FORMAT(fecha_creacion, '%Y-%m') AS periodo";
                $group = "GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')";
                $order = "ORDER BY periodo ASC";
                break;
        }

        $sql = $this->conexion->prepare("
        SELECT $select, COUNT(*) AS cantidad
        FROM usuario
        $where
        $group
        $order
    ");

        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }


}