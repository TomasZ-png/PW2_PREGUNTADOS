<?php

include_once ("MyDatabase.php");

class ConfigFactory{

    private $clases;

    public function __construct(){
        $this->clases['MyDatabase'] = new MyDatabase();
        $this->clases['Router'] = new Router($this, '', '');
    }

    public function getClase($nombreClase){
        return isset($this->clases[$nombreClase]) ? $this->clases[$nombreClase] : null;
    }

}