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

    public function registrarse($nombre, $fecha_nac, $sexo, $email, $password, $foto_perfil){

        $stmt = $this->conexion->prepare("SELECT 1 FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $usuarioExistente = ($stmt->get_result());

        if($usuarioExistente->num_rows > 0){
            return "El usuario con el correo ingresado ya existe";
        } else {
            $stmt2 = $this->conexion->prepare("INSERT INTO usuario (nombre_completo, correo, password, anio_nacimiento, sexo, foto_perfil, rol) 
                                            VALUES (?, ?, ?, ?, ?, ?, 'USER')");
            $stmt2->bind_param("ssssss", $nombre, $email, $password, $fecha_nac, $sexo, $foto_perfil);
            $result = $stmt2->execute();

            if($result){

                $id_usuario = $this->conexion->getConexion()->insert_id;

                $stmt3 = $this->conexion->prepare("SELECT u.id_usuario, u.nombre_completo, u.rol FROM usuario u WHERE correo = ?");
                $stmt3->bind_param("s", $email);
                $stmt3->execute();
                $result2 = ($stmt3->get_result())->fetch_assoc();

                $_SESSION['id'] = $id_usuario;
                $_SESSION['nombre'] = $result2['nombre_completo'];
                $_SESSION['rol'] = $result2['rol'];

                return "usuario registrado";
            } else {
                return "Error al registrar usuario";
            }
        }
    }
}