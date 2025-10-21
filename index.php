<?php

session_start();

include_once(__DIR__ . "/config/config.php");

include_once("../PROYECTO_PREGUNTADOS/helpers/ConfigFactory.php");
$configFactory = new ConfigFactory();

$router = $configFactory->getClase('Router');
$router->executeController($_GET["controller"], $_GET["method"]);