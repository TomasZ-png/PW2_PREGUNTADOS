
<?php

//  INICIO DE SESIÓN
session_start();

//  CARGA DE DEPENDENCIAS
include_once 'vendor/autoload.php';

include_once("ConfigFactory.php");

$configFactory = new ConfigFactory();
$router = $configFactory->getClase('Router'); 

$controllerParam = $_GET['controller'] ?? ''; 
$methodParam = $_GET['method'] ?? '';

// EJECUCIÓN DEL ROUTER

$router->executeController($controllerParam, $methodParam);

?>