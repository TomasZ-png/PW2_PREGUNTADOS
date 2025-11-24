<?php

class UsuarioModel
{
    private $conexion;

//    private $partidaModel;

    public function __construct($conexion){
        $this->conexion = $conexion;
//        $this->partidaModel = new PartidaModel($this->conexion);
    }


    public function getById($idUsuario){
        $sql = "SELECT id_usuario, rol, correo, nivel_usuario, verificado FROM usuario WHERE id_usuario = $idUsuario";
        $res = $this->conexion->query($sql);
        return $res[0] ?? null;
    }

   public function listarUsuarios(){
    $stmt = $this->conexion->prepare("
        SELECT id_usuario, nombre_completo, foto_perfil, puntaje_maximo_obtenido
        FROM usuario 
        WHERE rol = 'USER' 
        ORDER BY puntaje_maximo_obtenido DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

    // Nuevo método para obtener el puntaje máximo del usuario
    public function getPuntajeMaximo($id_usuario)
    {
        $sql = "SELECT puntaje_maximo_obtenido FROM usuario WHERE id_usuario = $id_usuario";
        $resultado = $this->conexion->query($sql);
        return $resultado[0]['puntaje_maximo_obtenido'] ?? 0;
    }

    //  Actualiza el puntaje máximo solo si es mayor
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

    public function contarPorSexo() {
        $stmt = $this->conexion->prepare("SELECT sexo, COUNT(*) as total FROM usuario GROUP BY sexo");

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

    public function obtenerTopUsuariosGlobales(){
        $stmt = $this->conexion->prepare('SELECT u.nombre_completo as nombre, u.puntaje_global as puntaje FROM usuario u
                                                                WHERE u.puntaje_global IS NOT NULL AND u.puntaje_global > 0 
                                            ORDER BY u.puntaje_global DESC LIMIT 10');
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
        return $usuarios;
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

    public function obtenerPartidasDelUsuario($idUsuario){
        return $this->conexion->query("SELECT * FROM historial_partidas_usuario h WHERE h.id_usuario = $idUsuario");
    }

    public function obtenerCantidadDePartidasJugadasDelUsuario($idUsuario){
        return count($this->obtenerPartidasDelUsuario($idUsuario));
    }

    public function obtenerPromedioDePuntosDePartidas($idUsuario){
        $partidas = $this->obtenerPartidasDelUsuario($idUsuario);

        if($partidas <= 5){
            return 0;
        }

        $puntajeTotalDePartidas = 0;
        $cantidadPartidas = 0;

        foreach ($partidas as $partida) {
            $puntajeTotalDePartidas += $partida['puntaje_final'];
            $cantidadPartidas++;
        }

        if ($cantidadPartidas == 0) {
            return 0;
        }

        return ($puntajeTotalDePartidas / 5) / $cantidadPartidas;
    }

    public function actualizarNivelDeUsuario($id_usuario){
        $puntajeAActualizar = $this->obtenerPromedioDePuntosDePartidas($id_usuario);

        if ($puntajeAActualizar == 0){
            return;
        }

        if($puntajeAActualizar <= 2.5){
            $nivelDeUsuario = 'APRENDIZ';
        } elseIf ($puntajeAActualizar > 2.5 && $puntajeAActualizar <= 4) {
            $nivelDeUsuario = 'NOVATO';
        } elseif ($puntajeAActualizar > 4 && $puntajeAActualizar <= 6) {
            $nivelDeUsuario = 'INTERMEDIO';
        } elseif ($puntajeAActualizar > 6 && $puntajeAActualizar <= 9) {
            $nivelDeUsuario = 'PROFESIONAL';
        } else {
            $nivelDeUsuario = 'ENTIDAD';
        }

        $sql = "UPDATE usuario u SET u.nivel_usuario = '$nivelDeUsuario' WHERE u.id_usuario = $id_usuario";
        $this->conexion->query($sql);
    }
}