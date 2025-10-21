<?php



class IncludeFileRenderer{
    public function __construct(){}

    public function renderWoHeader($template, $data = null){
        include_once (__DIR__.'/../views/'. $template .'Vista.php');
    }

    public function renderWHeader($template, $data = null){
        include_once (__DIR__.'/../views/partials/header.php');
        include_once (__DIR__.'/../views/'. $template .'Vista.php');
    }
}