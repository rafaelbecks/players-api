<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class EmpresasServiciosController
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
            $phql = 'SELECT * FROM EmpresasServicios'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'url' => $res->url,
                        'imagen_publicitaria' => $res->imagen_publicitaria,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'sector_empresarial' => $res->sector_empresarial,
                        'email' => $res->email,
                        'telefono' => $res->telefono,
                        'coordenadas' => $res->coordenadas,
                        'horas_trabajo' => $res->horas_trabajo,
                        'dias_trabajo' => $res->dias_trabajo,
                        'jugadores_en_cancha' => $res->jugadores_en_cancha,
                        'tarifa' => $res->tarifa,
                        'id_tipo_usuario' => TipoUsuario::find($res->id_tipo_usuario)
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

    public function search() {
        try {
            $params = $this->app->request->getQuery();
            
            $phql = 'SELECT * FROM EmpresasServicios WHERE estatus = 1 AND id_tipo_usuario = '.$params['id_tipo_usuario'].' AND id_pais= '.$params['id_pais'].' AND (nombre LIKE "%'.$params['nombre'].'%")';
            
            $resul = $this->app->modelsManager->executeQuery($phql);
            
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'url' => $res->url,
                        'imagen_publicitaria' => $res->imagen_publicitaria,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'sector_empresarial' => $res->sector_empresarial,
                        'email' => $res->email,
                        'telefono' => $res->telefono,
                        'coordenadas' => $res->coordenadas,
                        'horas_trabajo' => $res->horas_trabajo,
                        'dias_trabajo' => $res->dias_trabajo,
                        'jugadores_en_cancha' => $res->jugadores_en_cancha,
                        'tarifa' => $res->tarifa,
                        'id_tipo_usuario' => TipoUsuario::find($res->id_tipo_usuario)
                    ];
                }
                Commons::response(200 ,array("mensaje" => $data), $this->app);
            }else{
                Commons::response(200, array("mensaje"=> []), $this->app);
            }
        }catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los registros ".$e->getMessage()),$this->app);                                
        }
    }

    public function getOne($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM EmpresasServicios WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'url' => $res->url,
                        'imagen_publicitaria' => $res->imagen_publicitaria,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'sector_empresarial' => $res->sector_empresarial,
                        'email' => $res->email,
                        'telefono' => $res->telefono,
                        'coordenadas' => $res->coordenadas,
                        'horas_trabajo' => $res->horas_trabajo,
                        'dias_trabajo' => $res->dias_trabajo,
                        'jugadores_en_cancha' => $res->jugadores_en_cancha,
                        'tarifa' => $res->tarifa,
                        'id_tipo_usuario' => TipoUsuario::find($res->id_tipo_usuario)
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

    public function getEmpresaPorTipoUser($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM EmpresasServicios WHERE id_tipo_usuario = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'nombre' => $res->nombre,
                        'url' => $res->url,
                        'imagen_publicitaria' => $res->imagen_publicitaria,
                        'id_pais' => Paises::find($res->id_pais),
                        'id_estado' => Estados::find($res->id_estado),
                        'id_ciudad' => Ciudades::find($res->id_ciudad),
                        'estatus' => $res->estatus,
                        'sector_empresarial' => $res->sector_empresarial,
                        'email' => $res->email,
                        'telefono' => $res->telefono,
                        'coordenadas' => $res->coordenadas,
                        'horas_trabajo' => $res->horas_trabajo,
                        'dias_trabajo' => $res->dias_trabajo,
                        'jugadores_en_cancha' => $res->jugadores_en_cancha,
                        'tarifa' => $res->tarifa,
                        'id_tipo_usuario' => TipoUsuario::find($res->id_tipo_usuario)
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
            $phql = 'DELETE FROM EmpresasServicios WHERE id = '.$id;

            $partidos = $this->app->modelsManager->executeQuery($phql);

            Commons::response(200 ,array("mensaje" => "Registro eliminado"), $this->app);

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al eliminar el registro".$e->getMessage()),$this->app);                                
        }       
    }

    public function registrar() {
        try {
            $data = $this->app->request->getJsonRawBody();
            $resul = new EmpresasServicios();
            $data = (array)$data;
            if($resul->create($data)) {
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
            $resul = EmpresasServicios::find($id);
            $data = (array)$data;
            if(count($resul)>0){
                if(isset($data->imagen_publicitaria)){
                    $imagen_publicitaria=$data->imagen_publicitaria;
                    $img=uniqid($resul[0]->nombre);
                    Commons::base64ToImg($imagen_publicitaria, __DIR__."/../../images/$img");
                    $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                    $imagen_publicitaria=$img_url['local_url'].$img;
                    $data->imagen_publicitaria = $imagen_publicitaria;
                }
                
                if($resul->update($data)) {
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
            static::$instance = new EmpresasServiciosController($app);
        }
        return static::$instance;
    }

}

?>