<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class PaisesController
{

    public $app = NULL;
    public static $instance = NULL;


    public function __construct ($app)
    {
        $this->app = $app;
    }

    public function getAll()
    {
        try {
            $filtros = Commons::filterValidator($this->app->request->getQuery());
            $phql = 'SELECT * FROM Paises'.$filtros;

            $paises = $this->app->modelsManager->executeQuery($phql);
            if(count($paises)>0){
                $data = [];

                foreach ($paises as $pais) {
                    $data[] = [
                        'id'   => $pais->id,
                        'nombre' => $pais->nombre,
                        "nombre_corto" =>$pais->nombre_corto,
                        'estatus' => $pais->estatus
                    ];
                }

                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(404, array("mensaje" => "Aún no hay registros"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los registros ".$e->getMessage()),$this->app);                                
        }       
    }

    public function getOne($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM Paises WHERE id = '.$id;

            $paises = $this->app->modelsManager->executeQuery($phql);
            if(count($paises)>0){
                $data = [];

                foreach ($paises as $pais) {
                    $data[] = [
                        'id'   => $pais->id,
                        'nombre' => $pais->nombre,
                        "nombre_corto" => $pais->nombre_corto,
                        'estatus' => $pais->estatus
                    ];
                }

                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(404, array("mensaje" => "El registro no existe"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar el registro ".$e->getMessage()),$this->app);                                
        }       
    }

    public function delete($id = NULL)
    {
        try {
            $phql = 'DELETE FROM Paises WHERE id = '.$id;

            $paises = $this->app->modelsManager->executeQuery($phql);

            Commons::response(200 ,array("mensaje" => "Pais eliminado"), $this->app);

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new PaisesController($app);
        }
        return static::$instance;
    }

}

?>