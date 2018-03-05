<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class MensajesController
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

            $phql = 'SELECT * FROM Mensajes'.$filtros.' ORDER by timestamp ASC';

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'media' => $res->media,
                        'texto' => $res->texto,
                        'id_equipo' => $res->id_equipo != null ? Equipos::find($res->id_equipo):null,
                        'timestamp' => $res->timestamp,
                        'id_emisor' => $res->id_emisor,
                        'id_receptor' => $res->id_receptor,
                        'copia' => $res->copia,
                        'emisor' => Usuarios::find($res->id_emisor),
                        "receptor" => Usuarios::find($res->id_receptor),
                        'estatus' => $res->estatus,
                        'leido' => $res->leido
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
            $phql = 'SELECT * FROM Mensajes WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'media' => $res->media,
                        'texto' => $res->texto,
                        'id_equipo' => $res->id_equipo != null ? Equipos::find($res->id_equipo):null,
                        'timestamp' => $res->timestamp,
                        'id_emisor' => $res->id_emisor,
                        'copia' => $res->copia,
                        'emisor' => Usuarios::find($res->id_emisor),
                        'id_receptor' => $res->id_receptor,
                        'estatus' => $res->estatus,
                        'leido' => $res->leido
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
            $phql = 'DELETE FROM Mensajes WHERE id = '.$id;

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
            $resul = new Mensajes();
            if(isset($data->media)){
                $media=$data->media;
                $img=uniqid("chat-");
                Commons::base64ToImg($media, __DIR__."/../../images/".$img.".jpg");
                $data->media = $img.".jpg";
            }
            $data = (array)$data;
            if($resul->create($data)) {
                $copia = new Mensajes();
                $copiaData = $data;
                $copiaData["copia"] = $data["id_receptor"];
                $copiaData["leido"] = 0;

                if($copia->create($copiaData)){
                    Commons::response(201, array("mensaje" => $data) ,$this->app);
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
            $resul = Mensajes::find($id);
            if(count($resul)>0){
                if(isset($data->media)){
                    $media=$data->media;
                    $img=uniqid($resul[0]->nombre);
                    Commons::base64ToImg($media, __DIR__."/../../images/$img");
                    $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                    $media=$img_url['local_url'].$img;
                    $data->media = $media;
                }
                $data = (array)$data;
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

    public function markAsRead()
    {
        $data = $this->app->request->getJsonRawBody();

        foreach($data as $msg)
        {
            $phql = 'UPDATE Mensajes SET leido = 1 WHERE id ='.$msg->id;
            $resul = $this->app->modelsManager->executeQuery($phql);
        }
    }
    
    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new MensajesController($app);
        }
        return static::$instance;
    }

}

?>