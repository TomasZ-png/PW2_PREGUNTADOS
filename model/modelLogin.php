<?php

class LoginModel
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getUserWith($user, $password_plano) // $password es la contraseña en texto plano
    {
        // 1. SENTENCIA PREPARADA: Busca el usuario SOLO por el nombre (seguro contra Inyección SQL)
        $sql = "SELECT * FROM usuario WHERE usuario = ?"; // Usamos 'usuario' como nombre de tabla
        
        // Aquí debes usar un método seguro de tu DB. Si tu MyDatabase solo tiene query(), necesitarás un queryPreparada.
        // ASUMIMOS que $this->conexion->queryPreparada($sql, 's', [$user]) devuelve un array de filas.
        
        $resultado = $this->conexion->queryPreparada($sql, 's', [$user]); 

        if (empty($resultado)) {
            return false; // Usuario no encontrado
        }

        $usuario_db = $resultado[0];
        
        // 2. VERIFICACIÓN DE HASH: Compara la contraseña de texto plano con el hash almacenado
        if (password_verify($password_plano, $usuario_db['password'])) {
            return $usuario_db; // Login exitoso (devuelve los datos del usuario)
        } else {
            return false; // Contraseña incorrecta
        }
    }
}