<?php

// IMPORTANTE: Se debe usar 'use' statements para las clases de Composer
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class MustacheRenderer
{
    private $mustache;
    private $viewsFolder;

    /**
     * @param string $viewsFolder Ruta de la carpeta que contiene todas las vistas (ej: 'vista/').
     */
    public function __construct($viewsFolder)
    {
        // 1. CORRECCIÓN CRÍTICA: Se ELIMINA Mustache_Autoloader::register();
        // El autoloading lo maneja vendor/autoload.php en tu index.

        $this->viewsFolder = $viewsFolder;
        
        // 2. Configurar el motor de Mustache
        $this->mustache = new Mustache_Engine(
            array(
                // Solo se necesita configurar el loader para parciales, ya que el contenido principal 
                // se carga como string usando file_get_contents. 
                'partials_loader' => new Mustache_Loader_FilesystemLoader($viewsFolder),
            )
        );
    }

    /**
     * Método público llamado por el controlador (ej: $this->renderer->render("login", $datos)).
     * @param string $contentFile Nombre de la vista sin extensión (ej: 'login').
     * @param array $data Datos a inyectar en la plantilla.
     */
    public function render($contentFile , $data = array() )
    {
        // Construye la ruta exacta del archivo de contenido: vista/loginVista.mustache
        $contentPath = $this->viewsFolder . $contentFile . "Vista.mustache";
        
        // El resultado debe ser impreso al cliente
        echo $this->generateHtml($contentPath, $data);
    }

    /**
     * Carga el header, el contenido y el footer, los concatena y renderiza el resultado.
     */
    private function generateHtml($contentFile, $data = array())
    {
        // Asumiendo que tu carpeta de vistas tiene una estructura como:
        // vista/
        // ├── header.mustache
        // ├── loginVista.mustache <--- contenidoFile
        // └── footer.mustache
        
        // 1. Cargar header
        $contentAsString = file_get_contents($this->viewsFolder . 'header.mustache');
        
        // 2. Cargar el contenido de la vista
        $contentAsString .= file_get_contents($contentFile);
        
        // 3. Cargar footer
        $contentAsString .= file_get_contents($this->viewsFolder . 'footer.mustache');
        
        // 4. Renderizar la cadena HTML completa
        return $this->mustache->render($contentAsString, $data);
    }
}