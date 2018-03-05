<?php 

use Phalcon\Mvc\Micro\Collection as MicroCollection;

class UsersRouter
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
        $mc->setPrefix('/users');
        
        $mc->get('/', "getUser");
        $mc->get('/{email}', "getUser");
        $mc->put('/{email}', "updateUser");
        $mc->delete('/{email}', "deleteUser");
        $mc->post('/', "newUser");

        $this->app->mount($mc);
    }

    public static function getInstance($controller){
        if(is_null(static::$instance)){
            static::$instance = new UsersRouter($controller);
        }
        return static::$instance;
    }


}

?>