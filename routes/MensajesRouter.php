<?php 

use Phalcon\Mvc\Micro\Collection as MicroCollection;

class MensajesRouter
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
        $mc->setPrefix('/mensajes');
        
        $mc->get('/', "getAll");
        $mc->get('/{id:[0-9]+}', "getOne");
        $mc->post('/', "registrar");
        $mc->put('/mark-read', "markAsRead");
        $mc->put('/{id:[0-9]+}', "update");
        $mc->delete('/{id:[0-9]+}', "delete");

        $this->app->mount($mc);
    }

    public static function getInstance($controller){
        if(is_null(static::$instance)){
            static::$instance = new MensajesRouter($controller);
        }
        return static::$instance;
    }


}

?>