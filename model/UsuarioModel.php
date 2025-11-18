<?php

class UsuarioModel
{
    private $conexion;

    public function __construct($conexion){
        $this->conexion = $conexion;
    }


    public function listarUsuarios(){
        $stmt = $this->conexion->prepare("SELECT nombre_completo, foto_perfil, puntaje_maximo_obtenido
                                          FROM usuario 
                                          WHERE rol = 'USER' 
                                          ORDER BY puntaje_maximo_obtenido DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
        return $usuarios;
    }

    // Nuevo método para obtener el puntaje máximo del usuario
    public function getPuntajeMaximo($id_usuario)
    {
        $sql = "SELECT puntaje_maximo_obtenido FROM usuario WHERE id_usuario = $id_usuario";
        $resultado = $this->conexion->query($sql);
        return $resultado[0]['puntaje_maximo_obtenido'] ?? 0;
    }

    // NUEVO MÉTODO CLAVE: Actualiza el puntaje máximo solo si es mayor
    public function actualizarPuntajeMaximo($id_usuario, $puntaje_final)
    {
        // Se usa la función GREATEST para actualizar solo si el nuevo puntaje es mayor que el existente
        $sql = "UPDATE usuario 
                SET puntaje_maximo_obtenido = GREATEST(COALESCE(puntaje_maximo_obtenido, 0), ?)
                WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $puntaje_final, $id_usuario);
        $stmt->execute();
    }

    // ... (Mantener listarUsuarios y sumarPuntosUsuario si los usas para puntaje_global)
    public function sumarPuntosUsuario($id_usuario, $puntaje_final){
        $stmt = $this->conexion->prepare("UPDATE usuario 
                                           SET puntaje_global = COALESCE(puntaje_global, 0) + ? 
                                           WHERE id_usuario = ?");
        $stmt->bind_param("ii", $puntaje_final, $id_usuario);
        $stmt->execute();
    }
    public function guardarTokenVerificacion($email, $token)
    {
        $stmt = $this->conexion->prepare("UPDATE usuario u 
                                          SET u.token_verificado = ? 
                                          WHERE u.correo = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();
    }

    public function verificarCuenta($token){
        $stmt = $this->conexion->prepare("UPDATE usuario u
                                          SET u.verificado = true
                                          WHERE u.token_verificado = ?");

        $stmt->bind_param("s", $token);
        $stmt->execute();
    }

}