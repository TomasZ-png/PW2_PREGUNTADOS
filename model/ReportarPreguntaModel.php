<?php

class ReportarPreguntaModel {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtiene las preguntas jugadas en esa partida
    public function obtenerPreguntasDePartida($idPartida) {
        // 1) obtener la cadena
        $sql = "SELECT preguntas_jugadas FROM partida WHERE id_partida = ?";
        if (!$stmt = $this->conexion->prepare($sql)) {
            error_log("RPM: prepare falló (1): " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $idPartida);
        if (!$stmt->execute()) {
            error_log("RPM: execute falló (1): " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->bind_result($preguntasJugadas);
        $stmt->fetch();
        $stmt->close();

        if (empty($preguntasJugadas)) {
            // no hay preguntas jugadas -> devolvemos array vacío (la vista mostrará select vacío)
            return [];
        }

        // 2) preparar IDs - sanitizar a enteros y quitar vacíos
        $ids = array_filter(array_map('trim', explode(',', $preguntasJugadas)), function($v){ return $v !== ''; });
        if (count($ids) === 0) return [];

        // convertir a enteros
        $ids = array_map('intval', $ids);

        // 3) construir placeholders
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $sqlPreg = "SELECT id_pregunta, pregunta FROM pregunta WHERE id_pregunta IN ($placeholders)";
        $stmt = $this->conexion->prepare($sqlPreg);
        if (!$stmt) {
            error_log("RPM: prepare falló (2): " . $this->conexion->error . " SQL: $sqlPreg");
            return false;
        }

        // bind_param dinámico: necesita referencias
        $bind_names[] = $types;
        for ($i=0; $i<count($ids); $i++) {
            $bind_names[] = $ids[$i];
        }

        // convertir a referencias
        $refs = [];
        foreach ($bind_names as $key => $value) {
            $refs[$key] = &$bind_names[$key];
        }

        // call bind_param
        if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
            error_log("RPM: bind_param falló: " . $stmt->error);
            $stmt->close();
            return false;
        }

        if (!$stmt->execute()) {
            error_log("RPM: execute falló (2): " . $stmt->error);
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();
        if ($result === false) {
            error_log("RPM: get_result falló: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $preguntas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $preguntas;
    }

    // Guarda el reporte
    public function guardarReporte($idUsuario, $idPartida, $idPregunta, $motivo) {
        $sql = "INSERT INTO pregunta_reportada (id_pregunta, id_usuario, id_partida, motivo)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("RPM: prepare insertar reporte falló: " . $this->conexion->error);
            return false;
        }
        if (!$stmt->bind_param("iiis", $idPregunta, $idUsuario, $idPartida, $motivo)) {
            error_log("RPM: bind_param insertar reporte falló: " . $stmt->error);
            $stmt->close();
            return false;
        }
        if (!$stmt->execute()) {
            error_log("RPM: execute insertar reporte falló: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    }
}
