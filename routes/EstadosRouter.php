<?php 

use Phalcon\Mvc\Micro\Collection as MicroCollection;

class EstadosRouter
{

    private $app = NULL;
    private $controller = NULL;
    public static $instance = NULL;

    public function __construct ($controller)
    {
        $this->app = $controller->app;
        $this->controller = $controller;
        $this->init();
    }

    public function init()
    {
        $mc = new MicroCollection();

        $mc->setHandler($this->controller);
        $mc->setPrefix('/estados');
        
        $mc->get('/', "getAll");
        $mc->get('/{id:[0-9]+}', "getOne");
        $mc->get('/pais/{id:[0-9]+}', "getEstadosPorPais");
        $mc->delete('/{id:[0-9]+}', "delete");

        $this->app->mount($mc);
    }

    public static function getInstance($controller){
        if(is_null(static::$instance)){
            static::$instance = new EstadosRouter($controller);
        }
        return static::$instance;
    }


}

?>