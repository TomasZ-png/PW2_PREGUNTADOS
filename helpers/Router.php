<?php

class Router{

    private $configFactory;
    private $defaultController;
    private $defaultMethod;

    public function __construct($configFactory, $defaultController, $defaultMethod){
        $this->configFactory = $configFactory;
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
    }

    public function executeController($controllerParam, $methodParam){
        $controller = $this->getControllerFrom($controllerParam);
        $this->executeMethodFromController($controller, $methodParam);
    }

    private function getControllerFrom($controllerName){
        $controllerParam = $this->getControllerName($controllerName);
        $controller = $this->configFactory->getClase($controllerParam);

        if($controller == null){
            header('location: /PROYECTO_PREGUNTADOS/');
            exit();
        }

        return $controller;
    }

    private function executeMethodFromController($controller, $methodParam){
        call_user_func(array($controller, $this->getMethodName($controller, $methodParam)));
    }

    private function getControllerName($controllerName){
        return $controllerName ? $controllerName : $this->defaultController;
    }

    private function getMethodName($controllerName, $methodName){
        return method_exists($controllerName, $methodName) ? $methodName : $this->defaultMethod;
    }



}