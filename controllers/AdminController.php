<?php
include_once(__DIR__ . "/../model/UsuarioModel.php");
require_once (__DIR__ . '/../vendor/autoload.php');
include_once(__DIR__ . "/../model/AdminModel.php");
class AdminController {
    private $conexion;
    private $renderer;
    private $usuarioModel;
    private $preguntaModel;
    private $adminModel;

    public function __construct($conexion, $renderer){
        $this->conexion = $conexion;
        $this->renderer = $renderer;
        $this->usuarioModel = new UsuarioModel($this->conexion);
        $this->preguntaModel = new PreguntaModel($this->conexion);
        $this->adminModel = new AdminModel($this->conexion);

    }

    private function validarAdmin() {
        // Verifica sesión activa
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . BASE_URL . "LoginController/login");
            exit;
        }

        if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN' ) {
            header("Location: " . BASE_URL . "LoginController/login");
            exit;
        }

    }

    public function dashboardGraficos(){
        $this->validarAdmin();

        $datosSexo = $this->crearGraficoSexo();
        $datosPreguntasDificiles = $this->crearGraficoPreguntasDificilesYFaciles();
        $puntajeGlobal = $this->crearGraficoPuntajeGlobal();
        $cantidadUsuarios = $this->crearGraficoUsuariosTotales();
        $cantidadPartidas = $this->crearGraficoPartidasTotales();
        $cantidadPreguntasCreadas = $this->crearGraficoPreguntasCreadas();


        $this->renderer->render('adminGraficos', [
            'datosSexo' => json_encode($datosSexo, JSON_UNESCAPED_UNICODE),
            'datosPreguntas' => json_encode($datosPreguntasDificiles, JSON_UNESCAPED_UNICODE),
            'puntajeGlobal' => json_encode($puntajeGlobal, JSON_UNESCAPED_UNICODE),
            'cantidadUsuarios' => $cantidadUsuarios,
            'cantidadPartidas' => $cantidadPartidas,
            'cantidadPreguntasCreadas' => $cantidadPreguntasCreadas,
            'BASE_URL' => BASE_URL
        ]);
    }

    public function crearGraficoSexo(){
        $this->validarAdmin();
        $resultados = $this->usuarioModel->contarPorSexo();

        $data = [
            'Hombre' => 0,
            'Mujer' => 0,
            'No aclarado' => 0
        ];

        foreach ($resultados as $row) {
            $sexo = strtolower(trim($row['sexo']));
            $total = (int)$row['total'];

            if ($sexo === 'hombre') $data['Hombre'] = $total;
            elseif ($sexo === 'mujer') $data['Mujer'] = $total;
            else $data['No aclarado'] = $total;
        }

        return $data;
//        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
//
//        if ($json === false) {
//            $json = '{}';
//        }
//
//        $this->renderer->render('adminGraficoSexo', [
//            "datosJSON" => $json,
//            "BASE_URL" => BASE_URL
//        ]);
    }

    public function crearGraficoPreguntasDificilesYFaciles(){
        $this->validarAdmin();
        $resultado = $this->preguntaModel->obtenerPreguntasDeFacilesADificiles();

        $data = ['Pregunta', 'Acertadas', 'Erroneas'];

        foreach ($resultado as $row) {
            $data[] = [
                'pregunta' => $row['pregunta'],
                'acertadas' => (int)$row['acertadas'],
                'erroneas'  => (int)$row['erroneas']
            ];
        }
        return $data;
    }

    public function crearGraficoPuntajeGlobal(){
        $this->validarAdmin();

        $resultado = $this->usuarioModel->obtenerTopUsuariosGlobales();

        $data = [];
        $data[] = ['Usuario', 'Puntaje'];  // cabecera correcta

        foreach ($resultado as $row) {
            $data[] = [
                $row['nombre'],
                (int)$row['puntaje']
            ];
        }

        return $data;
    }

    public function crearGraficoUsuariosTotales(){
        $this->validarAdmin();

        $adminModel = new AdminModel($this->conexion);
        $usuariosTotales = $adminModel->obtenerCantidadUsuarios();
        $cantidadUsuarios = $usuariosTotales[0]['total'];

        return $cantidadUsuarios;
    }

    public function crearGraficoPartidasTotales(){
        $this->validarAdmin();

        $adminModel = new AdminModel($this->conexion);
        $partidasTotales = $adminModel->obtenerCantidadPartidas();
        $cantidadPartidas = $partidasTotales[0]['total'];

        return $cantidadPartidas;
    }

    public function crearGraficoPreguntasCreadas(){
        $this->validarAdmin();

        $preguntasCreadasTotales =  $this->adminModel->obtenerCantidadPreguntasCreadas();
        $cantidadPreguntasCreadas = $preguntasCreadasTotales[0]['total'];

        return $cantidadPreguntasCreadas;
    }

}