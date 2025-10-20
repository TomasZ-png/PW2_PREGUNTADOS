<?php

class LoginModel
{

    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function login($correo, $password){

        $stmt = $this->conexion->prepare("SELECT * FROM usuario WHERE correo = ? and password = ?");
        $stmt->bind_param("ss", $correo, $password);
        $stmt->execute();
        $result = ($stmt->get_result())->fetch_assoc();

        return $result;
    }
}