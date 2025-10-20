<?php

session_start();

include_once("ConfigFactory.php");
$configFactory = new ConfigFactory();

$router = $configFactory->getClase('Router');
$router->executeController($_GET["controller"], $_GET["method"]);