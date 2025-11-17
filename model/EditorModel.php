<?php

class EditorModel {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Helper: normaliza resultados de $this->conexion->query()
     * - Si query devuelve false => devuelve []
     * - Si devuelve array => lo devuelve tal cual
     * - Si devuelve mysqli_result => devuelve fetch_all(MYSQLI_ASSOC)
     */
    private function fetchAllSafe($result) {
        if ($result === false || $result === null) {
            return [];
        }

        if (is_array($result)) {
            return $result;
        }

        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    /**
     * Helper: obtener una fila asociativa segura
     */
    private function fetchAssocSafe($result) {
        if ($result === false || $result === null) {
            return null;
        }

        if (is_array($result)) {
            if (count($result) > 0) {
                return $result[0];
            }
            return null;
        }

        if ($result instanceof mysqli_result) {
            return $result->fetch_assoc();
        }

        return null;
    }



    public function getPreguntasNormales() {
        $sql = "SELECT id_pregunta, pregunta, categoria, puntaje 
                FROM pregunta
                WHERE categoria != 'INHABILITADA'";

        $result = $this->conexion->query($sql);
        return $this->fetchAllSafe($result);
    }

    /* ============================
       SUGERIDAS
       ============================ */

   public function getPreguntasSugeridas() {
    $sql = "SELECT * FROM pregunta_sugerida WHERE estado = 'PENDIENTE'";
    $result = $this->conexion->query($sql);
    return $this->fetchAllSafe($result);
}



    public function aceptarSugerencia($id) {
       
        $sql = "SELECT * FROM pregunta_sugerida WHERE id_pregunta_sugerida = ?";
        $stmt = $this->conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if (method_exists($stmt, 'get_result')) {
                $preg = $stmt->get_result()->fetch_assoc();
            } else {
                $preg = $this->fetchAssocSafe($this->conexion->query("SELECT * FROM pregunta_sugerida WHERE id_pregunta_sugerida = $id"));
            }

            $stmt->close();
        } else {
            $preg = $this->fetchAssocSafe($this->conexion->query("SELECT * FROM pregunta_sugerida WHERE id_pregunta_sugerida = $id"));
        }

        if (empty($preg)) {
            return false;
        }

        $stmt2 = $this->conexion->prepare("
            INSERT INTO pregunta (pregunta, categoria, dificultad, puntaje, cant_acertadas, cant_erroneas, fecha_creacion)
            VALUES (?, ?, 'MEDIA', 10, 0, 0, CURDATE())
        ");
        if (!$stmt2) {
            return false;
        }
        $stmt2->bind_param("ss", $preg['pregunta'], $preg['categoria']);
        $stmt2->execute();
        $idPreguntaNueva = $stmt2->insert_id;
        $stmt2->close();

        // traer respuestas sugeridas 
        $respResult = $this->conexion->query("SELECT * FROM respuesta_sugerida WHERE id_pregunta_sugerida = $id");
        $respuestas = $this->fetchAllSafe($respResult);

        foreach ($respuestas as $r) {
            $stmt3 = $this->conexion->prepare("
                INSERT INTO respuesta (respuesta, id_pregunta, es_correcta)
                VALUES (?, ?, ?)
            ");
            if ($stmt3) {
                $stmt3->bind_param("sii", $r['respuesta'], $idPreguntaNueva, $r['es_correcta']);
                $stmt3->execute();
                $stmt3->close();
            }
        }

        // marcar sugerencia como aceptada
        $this->conexion->query("UPDATE pregunta_sugerida SET estado='ACEPTADA' WHERE id_pregunta_sugerida=" . intval($id));

        return true;
    }

    public function rechazarSugerencia($id) {
        $this->conexion->query("UPDATE pregunta_sugerida SET estado='RECHAZADA' WHERE id_pregunta_sugerida=" . intval($id));
        return true;
    }


    public function getPreguntasReportadas() {
    $sql = "
        SELECT r.id_reporte, p.pregunta, p.id_pregunta, COUNT(r.id_reporte) AS cantidad_reportes
        FROM pregunta_reportada r
        INNER JOIN pregunta p ON p.id_pregunta = r.id_pregunta
        WHERE r.estado = 'PENDIENTE'
        GROUP BY r.id_pregunta
        HAVING COUNT(r.id_reporte) >= 0
    ";

    $result = $this->conexion->query($sql);
    return $this->fetchAllSafe($result);
}



    public function aceptarReporte($idReporte) {
        $idReporte = intval($idReporte);
        // marcar reporte revisado 
        $this->conexion->query("UPDATE pregunta_reportada SET estado='REVISADA' WHERE id_reporte=$idReporte");

        // obtener id_pregunta a partir del id_reporte 
        $fila = $this->fetchAssocSafe($this->conexion->query("SELECT id_pregunta FROM pregunta_reportada WHERE id_reporte=$idReporte"));
        if (!empty($fila) && isset($fila['id_pregunta'])) {
            $idPregunta = intval($fila['id_pregunta']);
            // inhabilitar pregunta
            $this->conexion->query("UPDATE pregunta SET categoria='INHABILITADA' WHERE id_pregunta=$idPregunta");
        }
        return true;
    }

    public function rechazarReporte($idReporte) {
        $idReporte = intval($idReporte);
        $this->conexion->query("UPDATE pregunta_reportada SET estado='REVISADA' WHERE id_reporte=$idReporte");
        return true;
    }


    public function getPreguntaCompleta($id) {
        $id = intval($id);
        $pregRow = $this->fetchAssocSafe($this->conexion->query("SELECT * FROM pregunta WHERE id_pregunta=$id"));
        if (!$pregRow) {
            return null;
        }

        $resp = $this->fetchAllSafe($this->conexion->query("SELECT * FROM respuesta WHERE id_pregunta=$id"));

        return [
            'id_pregunta' => $pregRow['id_pregunta'],
            'pregunta' => $pregRow['pregunta'],
            'categoria' => $pregRow['categoria'],
            'puntaje' => $pregRow['puntaje'],
            'respuestas' => $resp
        ];
    }

    public function guardarEdicion($id, $texto, $categoria, $puntaje, $respuestas, $correcta) {
        $id = intval($id);
        $stmt = $this->conexion->prepare("
            UPDATE pregunta SET pregunta=?, categoria=?, puntaje=? WHERE id_pregunta=?
        ");
        if ($stmt) {
            $stmt->bind_param("ssii", $texto, $categoria, $puntaje, $id);
            $stmt->execute();
            $stmt->close();
        }

        if (is_array($respuestas)) {
            foreach ($respuestas as $idRespuesta => $textoResp) {
                $idRespuesta = intval($idRespuesta);
                $correctaFlag = ($idRespuesta == intval($correcta)) ? 1 : 0;

                $stmt2 = $this->conexion->prepare("
                    UPDATE respuesta SET respuesta=?, es_correcta=? WHERE id_respuesta=?
                ");
                if ($stmt2) {
                    $stmt2->bind_param("sii", $textoResp, $correctaFlag, $idRespuesta);
                    $stmt2->execute();
                    $stmt2->close();
                }
            }
        }

        return true;
    }
}
