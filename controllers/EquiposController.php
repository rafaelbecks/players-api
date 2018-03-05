<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class EquiposController
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

            $phql = 'SELECT * FROM Equipos'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $sqlCount = "SELECT COUNT(id) AS total_jugadores FROM UsuariosXEquipos WHERE id_equipo = ".$res->id." AND estatus = 1";
                    $total = $this->app->modelsManager->executeQuery($sqlCount);
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'descripcion' => $res->descripcion,
                        'fecha_fundacion' => $res->fecha_fundacion,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'imagen_equipo' => $res->imagen_equipo,
                        'id_admin' => Usuarios::find($res->id_admin),
                        'total_jugadores' => $total[0]->total_jugadores
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
            $phql = 'SELECT * FROM Equipos WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'descripcion' => $res->descripcion,
                        'fecha_fundacion' => $res->fecha_fundacion,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'imagen_equipo' => $res->imagen_equipo,
                        'id_admin' => Usuarios::find($res->id_admin)
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
            $phql = 'DELETE FROM Equipos WHERE id = '.$id;

            $partidos = $this->app->modelsManager->executeQuery($phql);
            var_dump($partidos);
            if($partidos) {
                $query_equipos = "DELETE FROM UsuariosXEquipos WHERE id_equipo =".$id;
                if($query_equipos){
                    $query_mensajes = "DELETE FROM Mensajes WHERE id_equipo =".$id;
                    if($query_mensajes) {
                        Commons::response(200 ,array("mensaje" => "Registro eliminado"), $this->app);
                    }
                }
            }
        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public function getFromController() {
        $filtros = Commons::filterValidator($this->app->request->getQuery());

        $phql = 'SELECT * FROM Equipos'.$filtros;

        $resul = $this->app->modelsManager->executeQuery($phql);
        if(count($resul)>0){
            $data = [];

            foreach ($resul as $res) {
                $data[] = [
                    'id'   => $res->id,
                    'nombre' => $res->nombre,
                    'descripcion' => $res->descripcion,
                    'fecha_fundacion' => $res->fecha_fundacion,
                    'id_pais' => $res->id_pais,
                    'id_estado' => $res->id_estado,
                    'id_ciudad' => $res->id_ciudad,
                    'estatus' => $res->estatus,
                    'imagen_equipo' => $res->imagen_equipo,
                    'id_admin' => $res->id_admin
                ];
            }
        }

            return $data;
    }

    public function registrar() {
        try {
            $data = $this->app->request->getJsonRawBody();
            $resul = new Equipos();
            $data = (array)$data;
            if(isset($data["imagen_equipo"])){
                $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                $file_name = uniqid("pf")."_".$data[0]->nombre.".jpg";
                $output = $img_url["absolute_url"].$file_name;
                $img = Commons::base64ToImg($data["imagen_equipo"],$output);
                $data["imagen_equipo"] = $file_name;
            }
            if($resul->create($data)) {
                $newTeam = $this->getFromController(array("nombre"=>$data["nombre"]));
                $newRelation = array(
                    "id_equipo" => $newTeam[0]["id"],
                    "id_usuario" => $newTeam[0]["id_admin"],
                    "estatus" => 1
                );
                $userEquipos = new UsuariosXEquipos;
                if($userEquipos->create($newRelation))
                {
                    Commons::response(201, array("mensaje" => $data) ,$this->app);
                }else{
                    Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
                }
            } 
        }catch(PDOException $e){
            echo $e;
            Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
        }
    }

    public function update($id = null) {
        try {

            $data = $this->app->request->getJsonRawBody();
            $resul = Equipos::find($id);
            $data = (array)$data;
            if(count($resul)>0){
                if(isset($data["imagen_equipo"])){
                    $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                    $file_name = uniqid("pf")."_".$resul[0]->id.".jpg";
                    $output = $img_url["absolute_url"].$file_name;
                    $img = Commons::base64ToImg($data["imagen_equipo"],$output);
                    $data["imagen_equipo"] = $file_name;
                }
                
                if($resul->update($data)) {
                    Commons::response(200, array("mensaje" => $data) ,$this->app);
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
            static::$instance = new EquiposController($app);
        }
        return static::$instance;
    }

}

?>