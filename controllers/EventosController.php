<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class EventosController
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
            $phql = 'SELECT * FROM Eventos'.$filtros;

            $partidos = $this->app->modelsManager->executeQuery($phql);
            if(count($partidos)>0){
                $data = [];

                foreach ($partidos as $partido) {
                    $data[] = [
                        'id'   => $partido->id,
                        'titulo' => $partido->titulo,
                        'id_pais' => Paises::find($partido->id_pais),
                        'id_estado' => Estados::find($partido->id_estado),
                        'id_ciudad' => Ciudades::find($partido->id_ciudad),
                        'direccion' => $partido->direccion,
                        'fecha_partido' => $partido->fecha_partido,
                        'hora_partido' => $partido->hora_partido,
                        'monto_pagar' => $partido->monto_pagar,
                        'jugadores_en_cancha' => $partido->jugadores_en_cancha,
                        'estatus' => $partido->estatus,
                        'id_cancha' => $partido->id_cancha,
                        'id_arbitro' => $partido->id_arbitro,
                        'id_tipo_evento' => $partido->id_tipo_evento,
                        'id_equipo_ganador' => $partido->id_equipo_ganador,
                        'tag_identificador' => $partido->tag_identificador,
                        'nombre_arbitro' => $partido->nombre_arbitro,
                        'nombre_cancha' => $partido->nombre_cancha,
                        'id_admin' => Usuarios::find($partido->id_admin)
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
            $phql = 'SELECT * FROM Eventos WHERE id = '.$id;

            $partidos = $this->app->modelsManager->executeQuery($phql);
            if(count($partidos) > 0){
                $data = [];

                foreach ($partidos as $partido) {
                    $data[] = [
                        'id'   => $partido->id,
                        'titulo' => $partido->titulo,
                        'id_pais' => Paises::find($partido->id_pais),
                        'id_estado' => Estados::find($partido->id_estado),
                        'id_ciudad' => Ciudades::find($partido->id_ciudad),
                        'direccion' => $partido->direccion,
                        'fecha_partido' => $partido->fecha_partido,
                        'hora_partido' => $partido->hora_partido,
                        'monto_pagar' => $partido->monto_pagar,
                        'jugadores_en_cancha' => $partido->jugadores_en_cancha,
                        'estatus' => $partido->estatus,
                        'id_cancha' => $partido->id_cancha,
                        'id_arbitro' => $partido->id_arbitro,
                        'id_tipo_evento' => $partido->id_tipo_evento,
                        'id_equipo_ganador' => $partido->id_equipo_ganador,
                        'tag_identificador' => $partido->tag_identificador,
                        'nombre_arbitro' => $partido->nombre_arbitro,
                        'nombre_cancha' => $partido->nombre_cancha,
                        'id_admin' => Usuarios::find($partido->id_admin)
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

    public function getEventosPorCiudad($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM Eventos WHERE id_ciudad = '.$id;

            $partidos = $this->app->modelsManager->executeQuery($phql);
            if(count($partidos)>0){
                $data = [];

                foreach ($partidos as $partido) {
                    $data[] = [
                        'id'   => $partido->id,
                        'titulo' => $partido->titulo,
                        'id_pais' => Paises::find($partido->id_pais),
                        'id_estado' => Estados::find($partido->id_estado),
                        'id_ciudad' => Ciudades::find($partido->id_ciudad),
                        'direccion' => $partido->direccion,
                        'fecha_partido' => $partido->fecha_partido,
                        'hora_partido' => $partido->hora_partido,
                        'monto_pagar' => $partido->monto_pagar,
                        'jugadores_en_cancha' => $partido->jugadores_en_cancha,
                        'estatus' => $partido->estatus,
                        'id_cancha' => $partido->id_cancha,
                        'id_arbitro' => $partido->id_arbitro,
                        'id_tipo_evento' => $partido->id_tipo_evento,
                        'id_equipo_ganador' => $partido->id_equipo_ganador,
                        'tag_identificador' => $partido->tag_identificador,
                        'nombre_arbitro' => $partido->nombre_arbitro,
                        'nombre_cancha' => $partido->nombre_cancha,
                        'id_admin' => Usuarios::find($partido->id_admin)
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
            
            $phql1 = "DELETE FROM EventosXInvitados WHERE id_evento =".$id;
            if($partidos = $this->app->modelsManager->executeQuery($phql1)){
                $phql2 = 'DELETE FROM Eventos WHERE id = '.$id;
                $partidos2 = $this->app->modelsManager->executeQuery($phql2);
            
            Commons::response(200 ,array("mensaje" => "Registro eliminado"), $this->app);
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public function registrar() {
        try {
            $data = $this->app->request->getJsonRawBody();
            $evento = new Eventos();
            $data = (array)$data;
            if($evento->create($data)) {
                $data['id'] = $evento->id;
                Commons::response(201, array("mensaje" => $data) ,$this->app);
            }
        }catch(PDOException $e){
            echo $e;
            Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
        }
    }

    public function update($id = null) {
        try {

            $data = $this->app->request->getJsonRawBody();
            $evento = Eventos::find($id);
            if(count($evento)>0){
                $data = (array)$data;
                if($evento->update($data)) {
                    Commons::response(201, array("mensaje" => $data) ,$this->app);
                } 
            }else{
                Commons::response(404, array("mensaje" => "El registro no existe"), $this->app);
            }
        }catch(PDOException $e){
            echo $e;
            Commons::response(500,array("mensaje" => "Ocurrió un error al editar la data".$e->getMessage()),$this->app);
        }
    }
    
    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new EventosController($app);
        }
        return static::$instance;
    }

}

?>