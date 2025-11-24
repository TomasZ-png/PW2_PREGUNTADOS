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

        $filtro = $_GET['filtro'] ?? 'mes'; // por ejemplo

        $selectData = [
            'isDia' => $filtro == 'dia',
            'isSemana' => $filtro == 'semana',
            'isMes' => $filtro == 'mes',
            'isAnio' => $filtro == 'anio'
        ];



        $datosSexo = $this->crearGraficoSexo($filtro);
        $datosPreguntasDificiles = $this->crearGraficoPreguntasDificilesYFaciles($filtro);
        $puntajeGlobal = $this->crearGraficoPuntajeGlobal($filtro);
        $cantidadUsuarios = $this->crearGraficoUsuariosTotales($filtro);
        $cantidadPartidas = $this->crearGraficoPartidasTotales($filtro);
        $cantidadPreguntasCreadas = $this->crearGraficoPreguntasCreadas($filtro);
        $cantidadPreguntasTotales = $this->crearGraficoPreguntasTotales($filtro);
        $usuariosPorEdad =  $this-> crearGraficoUsuariosPorEdad($filtro);
        $usuariosPorPais = $this->crearGraficoUsuariosPorPais($filtro);
        $cantUsuariosNuevos = $this->crearGraficoUsuariosNuevos($filtro);


        $this->renderer->render('adminGraficos', [
            'datosSexo' => json_encode($datosSexo, JSON_UNESCAPED_UNICODE),
            'datosPreguntas' => json_encode($datosPreguntasDificiles, JSON_UNESCAPED_UNICODE),
            'puntajeGlobal' => json_encode($puntajeGlobal, JSON_UNESCAPED_UNICODE),
            'cantidadUsuarios' => $cantidadUsuarios,
            'cantidadPartidas' => $cantidadPartidas,
            'cantidadPreguntasCreadas' => $cantidadPreguntasCreadas,
            'cantidadPreguntasTotales' => $cantidadPreguntasTotales,
            'usuariosPorEdad' => json_encode($usuariosPorEdad, JSON_UNESCAPED_UNICODE),
            'usuariosPorPais' => json_encode($usuariosPorPais, JSON_UNESCAPED_UNICODE),
            'usuariosNuevos' => json_encode($cantUsuariosNuevos, JSON_UNESCAPED_UNICODE),
            'select' => $selectData,
            'filtro' => $filtro, // por si hace falta en el front
            'BASE_URL' => BASE_URL
        ]);
    }

    public function crearGraficoSexo(){
        $this->validarAdmin();
        $resultados = $this->usuarioModel->contarPorSexo();

        $data = [
            'masculino' => 0,
            'femenino' => 0,
            'No aclarado' => 0
        ];

        foreach ($resultados as $row) {
            $sexo = strtolower(trim($row['sexo']));
            $total = (int)$row['total'];

            if ($sexo === 'masculino') $data['masculino'] = $total;
            elseif ($sexo === 'femenino') $data['femenino'] = $total;
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

    public function crearGraficoPreguntasTotales(){
        $this->validarAdmin();

        $preguntasTotales =  $this->adminModel->obtenerCantidadPreguntasTotales();
        $cantidadPreguntasTotales = $preguntasTotales[0]['total'];

        return $cantidadPreguntasTotales;
    }

    public function crearGraficoUsuariosPorEdad()
    {
        $this->validarAdmin();

        $resultado = $this->adminModel->obtenerCantidadUsuariosPorEdad();

        $data = [];
        $data[] = ['Grupo', 'Cantidad'];

        $data[] = ['Menores', (int)$resultado['menores']];
        $data[] = ['Medios', (int)$resultado['medios']];
        $data[] = ['Jubilados', (int)$resultado['jubilados']];

        return $data;
    }

    public function crearGraficoUsuariosPorPais(){
        $this->validarAdmin();

        $datos = $this->adminModel->obtenerJugadoresPorPais();
        return $datos;
    }

    public function crearGraficoUsuariosNuevos($filtro)
    {
        $this->validarAdmin();

        $resultados = $this->adminModel->obtenerUsuariosNuevos($filtro);

        // Google Charts necesita ARRAY DE OBJETOS
        return $resultados;
    }

}