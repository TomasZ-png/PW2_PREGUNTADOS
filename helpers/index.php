
<?php
session_start();
include_once '../vendor/autoload.php';

include_once("ConfigFactory.php");

$configFactory = new ConfigFactory();
$router = $configFactory->getClase('Router'); 

$controllerParam = isset($_GET['controller']) ? $_GET['controller'] : '';
$methodParam = isset($_GET['method']) ? $_GET['method'] : '';

// EJECUCIÓN DEL ROUTER

$router->executeController($controllerParam, $methodParam);

