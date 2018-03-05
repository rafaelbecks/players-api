<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class CiudadesController
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

            $phql = 'SELECT * FROM Ciudades'.$filtros;

            $ciudades = $this->app->modelsManager->executeQuery($phql);
            if(count($ciudades)>0){
                $data = [];

                foreach ($ciudades as $ciudad) {
                    $data[] = [
                        'id'   => $ciudad->id,
                        'nombre' => $ciudad->nombre,
                        'id_estado' => Estados::find($ciudad->id_estado),
                        'estatus' => $ciudad->estatus
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
            $phql = 'SELECT * FROM Ciudades WHERE id = '.$id;

            $ciudades = $this->app->modelsManager->executeQuery($phql);
            if(count($ciudades)>0){
                $data = [];

                foreach ($ciudades as $ciudad) {
                    $data[] = [
                        'id'   => $ciudad->id,
                        'nombre' => $ciudad->nombre,
                        'id_estado' => Estados::find($ciudad->id_estado),
                        'estatus' => $ciudad->estatus
                    ];
                }

                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(404, array("mensaje" => "El registro no existe"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public function getCiudadPorEstado($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM Ciudades WHERE id_estado = '.$id;

            $ciudades = $this->app->modelsManager->executeQuery($phql);
            if(count($ciudades)>0){
                $data = [];

                foreach ($ciudades as $ciudad) {
                    $data[] = [
                        'id'   => $ciudad->id,
                        'nombre' => $ciudad->nombre,
                        'id_estado' => Estados::find($ciudad->id_estado),
                        'estatus' => $ciudad->estatus
                    ];
                }

                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(404, array("mensaje" => "Aún no hay registros"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los registros".$e->getMessage()),$this->app);                                
        }       
    }

    public function delete($id = NULL)
    {
        try {
            $phql = 'DELETE FROM Ciudades WHERE id = '.$id;

            $ciudades = $this->app->modelsManager->executeQuery($phql);

            Commons::response(200 ,array("mensaje" => "Registro eliminado"), $this->app);

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new CiudadesController($app);
        }
        return static::$instance;
    }

}

?>