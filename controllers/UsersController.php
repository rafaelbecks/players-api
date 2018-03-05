<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class UsersController
{

    public $app = NULL;
    public static $instance = NULL;

    public function __construct ($app)
    {
        $this->app = $app;
    }

    public function getUser($email = null, $fromInsert = false)
    {
        echo 'users';
        try {
            Commons::response(200,array("mensaje" => "Ocurrió un error al listar los usuarios ".$e->getMessage()),$this->app); 

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los usuarios ".$e->getMessage()),$this->app);                                
        }       
    }

    public function updateUser($email)
    {

    }

    public function newUser()
    {

    }

    public function deleteUser($email)
    {


    }

    public function auth($credentials)
    {

        $usuario = []; //Agregar aquí consulta con credenciales de usuarios 
        
        if( is_array($usuario) && count($usuario)>0 )
        {
            return $usuario;
        }else
        {
            return false;
        }
    }

    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new UsersController($app);
        }
        return static::$instance;
    }

}

?>