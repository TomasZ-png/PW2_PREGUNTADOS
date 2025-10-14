<?php

session_start();

include_once("ConfigFactory.php");
$configFactory = new ConfigFactory();

$router = $configFactory->getClase('router');