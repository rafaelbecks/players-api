<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class UsuariosXLikesController
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

            $phql = 'SELECT * FROM UsuariosXLikes'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_post' => Posts::find($res->id_post),
                        'id_usuario' => Usuarios::find($res->id_usuario),
                        'timestamp' => $res->timestamp
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
            $phql = 'SELECT * FROM UsuariosXLikes WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_post' => Posts::find($res->id_post),
                        'id_usuario' => Usuarios::find($res->id_usuario),
                        'timestamp' => $res->timestamp
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
            $phql = 'DELETE FROM UsuariosXLikes WHERE id = '.$id;

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
            $evento = new UsuariosXLikes();
            $data = (array)$data;
            if($evento->create($data)) {
                $phql = "SELECT COUNT(id) as total_likes FROM UsuariosXLikes WHERE id_post = ".$data["id_post"];
                $resul = $this->app->modelsManager->executeQuery($phql);
                if(count($resul)>0){
                    $post = new Posts();
                    $data_post = [
                        "likes"=>(integer)$resul[0]["total_likes"],
                        "id"=>$data["id_post"]
                    ];
                    $id_like = $evento->id;
                    $resulP = Posts::find($data["id_post"]);
                   
                    if($resulP->update($data_post)){
                        $data["total_likes"] = $data_post["likes"];
                        $data["id"] = $id_like;
                        Commons::response(201 ,array("mensaje" => $data), $this->app);
                    }
                }else{
                    Commons::response(500, array("mensaje" => "Error al dar like") ,$this->app);
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
            $resul = UsuariosXLikes::find($id);
            if(count($resul)>0){
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
    
    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new UsuariosXLikesController($app);
        }
        return static::$instance;
    }

}

?>