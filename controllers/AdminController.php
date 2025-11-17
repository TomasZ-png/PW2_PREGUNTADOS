<?php
include_once(__DIR__ . "/../model/UsuarioModel.php");
require_once __DIR__ . '/../vendor/autoload.php';
class AdminController {
    private $conexion;
    private $renderer;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
    }

    private function validarAdmin() {
        // Verifica sesión activa
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "/LoginController/login");
            exit;
        }

        $usuarioModel = new UsuarioModel($this->conexion);
        $usuario = $usuarioModel->obtenerUsuario($_SESSION['id_usuario']);

        if (!$usuario || !isset($usuario['rol']) || strtoupper(trim($usuario['rol'])) !== 'ADMIN') {
            header("Location: " . BASE_URL);
            exit;
        }

        // ✅ Si llegamos hasta aquí, es admin, podemos continuar
    }

    public function crearGraficoSexo() {
        $this->validarAdmin();

        $usuarioModel = new UsuarioModel($this->conexion);
        $sexoCount = $usuarioModel->contarPorSexo();

        // Transformar a formato asociativo
        $data = [
            'Masculino' => 0,
            'Femenino' => 0,
            'No aclarado' => 0
        ];

        foreach ($sexoCount as $row) {
            $sexo = strtolower(trim($row['sexo']));
            if ($sexo === 'masculino') $data['Masculino'] = (int)$row['cantidad'];
            elseif ($sexo === 'femenino') $data['Femenino'] = (int)$row['cantidad'];
            else $data['No aclarado'] = (int)$row['cantidad'];
        }

        // Pasar los datos a la vista Mustache
        echo $this->renderer->render('adminGraficoSexoVista', [
            'sexoCount' => $data
        ]);
    }


    public function generarGraficoSexoImg() {
        $this->validarAdmin();

        $usuarioModel = new UsuarioModel($this->conexion);
        $resultados = $usuarioModel->contarPorSexo();

        $sexoCount = [
            'Masculino' => 0,
            'Femenino' => 0,
            'No aclarado' => 0
        ];

        foreach ($resultados as $row) {
            $sexo = strtolower(trim($row['sexo']));
            $total = (int)$row['total'];
            if ($sexo === 'masculino')$sexoCount['Masculino'] = $total;
            elseif ($sexo === 'femenino')  $sexoCount['Femenino'] = $total;
            else $data['No aclarado'] = $total;
        }

        $data = array_values($sexoCount);
        $labels = array_keys($sexoCount);

        $graph = new PieGraph(1000, 800);
        $graph->SetShadow();

        $p1 = new PiePlot3D($data);
        $p1->SetLegends($labels);
        $p1->SetTheme('sand');

        $graph->Add($p1);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }
    public function vistaGraficos() {
        $this->redirectToLogin();
        $this->renderer->render("adminGraficoSexoVista");
    }

}

