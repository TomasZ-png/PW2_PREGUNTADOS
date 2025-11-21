<?php

ob_clean(); //Limpia cualquier salida previa

require_once __DIR__ . '/../phpqrcode/qrlib.php';

class QRCodeController {

    private $db;
    private $renderer;

    public function __construct($db, $renderer){
        $this->db = $db;
        $this->renderer = $renderer;
    }

    public function generar(){

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo "Falta el parámetro id";
            return;
        }

        $id = intval($_GET['id']);

        // URL del perfil del usuario
        $url = BASE_URL . "?controller=PerfilController&method=verPerfilPublico&id=" . $id;

        // Avisamos que devolvemos un PNG
        header('Content-Type: image/png');

        // Generamos el QR
        QRcode::png($url, null, QR_ECLEVEL_L, 3, 1);
    }
}
