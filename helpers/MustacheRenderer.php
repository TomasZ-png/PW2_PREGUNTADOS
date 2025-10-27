<?php

class MustacheRenderer{
    private $mustache;
    private $viewsFolder;

    public function __construct($partialsPathLoader){
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
                'partials_loader' => new Mustache_Loader_FilesystemLoader( $partialsPathLoader )
            ));
        $this->viewsFolder = $partialsPathLoader;
    }

    public function render($contentFile , $data = array() ){
        echo  $this->generateHtml(  $this->viewsFolder . '/' . $contentFile . "Vista.mustache" , $data);
    }

    public function generateHtml($contentFile, $data = array()) {
        $contentAsString = file_get_contents(  $this->viewsFolder .'/partials/header.mustache');
        $contentAsString .= file_get_contents( $contentFile );
        $contentAsString .= file_get_contents($this->viewsFolder . '/partials/footer.mustache');
        return $this->mustache->render($contentAsString, $data);
    }
}