<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class EstadosController
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
            $phql = 'SELECT * FROM Estados'.$filtros;

            $estados = $this->app->modelsManager->executeQuery($phql);
            if(count($estados)>0){
                $data = [];

                foreach ($estados as $estado) {
                    $data[] = [
                        'id'   => $estado->id,
                        'nombre' => $estado->nombre,
                        'id_pais' => Paises::find($estado->id_pais),
                        'estatus' => $estado->estatus
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
            $phql = 'SELECT * FROM Estados WHERE id = '.$id;

            $estados = $this->app->modelsManager->executeQuery($phql);
            if($count($estados>0)){
                $data = [];

                foreach ($estados as $estado) {
                    $data[] = [
                        'id'   => $estado->id,
                        'nombre' => $estado->nombre,
                        'id_pais' => Paises::find($estado->id_pais),
                        'estatus' => $estado->estatus
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

    public function getEstadosPorPais($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM Estados WHERE id_pais = '.$id;

            $estados = $this->app->modelsManager->executeQuery($phql);
            if(count($estados)>0){
                $data = [];

                foreach ($estados as $estado) {
                    $data[] = [
                        'id'   => $estado->id,
                        'nombre' => $estado->nombre,
                        'id_pais' => Paises::find($estado->id_pais),
                        'estatus' => $estado->estatus
                    ];
                }

                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(404, array("mensaje" => "El registro no existe"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los registros".$e->getMessage()),$this->app);                                
        }       
    }

    public function delete($id = NULL)
    {
        try {
            $phql = 'DELETE FROM Estados WHERE id = '.$id;

            $estados = $this->app->modelsManager->executeQuery($phql);

            Commons::response(200 ,array("mensaje" => "Registro eliminado"), $this->app);

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new EstadosController($app);
        }
        return static::$instance;
    }

}

?>