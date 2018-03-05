<?php 

use Phalcon\Mvc\Micro\Collection as MicroCollection;

class EmpresasServiciosRouter
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
        $mc->setPrefix('/empresas');
        
        $mc->get('/', "getAll");
        $mc->get('/{id:[0-9]+}', "getOne");
        $mc->get('/search', "search");
        $mc->get('/tipo_user/{id:[0-9]+}', "getEmpresaPorTipoUser");
        $mc->post('/', "registrar");
        $mc->put('/{id:[0-9]+}', "update");
        $mc->delete('/{id:[0-9]+}', "delete");

        $this->app->mount($mc);
    }

    public static function getInstance($controller){
        if(is_null(static::$instance)){
            static::$instance = new EmpresasServiciosRouter($controller);
        }
        return static::$instance;
    }


}

?>