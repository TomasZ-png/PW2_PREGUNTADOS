<?php

class MyDatabase
{

    private $conexion;

    public function __construct(){

        $config = parse_ini_file(__DIR__ . "/../config/config.ini");

        $this->conexion = new mysqli(
            $config["server"],
            $config["user"],
            $config["pass"],
            $config["database"]
        );

        if($this->conexion->connect_error){
            echo 'Error al conectar la base de datos. Error: ' . $this->conexion->connect_error;
        }
    }

    public function query($query){
        $resultado = $this->conexion->query($query);

        if(!$resultado){
            die ("Error en la query $query");
        }

        if($resultado instanceof mysqli_result) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            return $resultado;
        }
    }

    public function getConexion(){
        return $this->conexion;
    }

}