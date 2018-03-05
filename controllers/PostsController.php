<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class PostsController
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

            $phql = 'SELECT * FROM Posts'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'marcado_indebido' => $res->marcado_indebido,
                        'likes' => $res->likes,
                        'texto' => $res->texto,
                        'media' => $res->media,
                        'id_usuario' => Usuarios::find($res->id_usuario),
                        'id_usuario_comparte' => Usuarios::find($res->id_usuario_comparte),
                        'estatus' => $res->estatus
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
            $phql = 'SELECT * FROM Posts WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'marcado_indebido' => $res->marcado_indebido,
                        'likes' => $res->likes,
                        'texto' => $res->texto,
                        'media' => $res->media,
                        'id_usuario' => Usuarios::find($res->id_usuario),
                        'id_usuario_comparte' => Usuarios::find($res->id_usuario_comparte),
                        'estatus' => $res->estatus
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

    public function getPostsByUser($id = NULL)
    {
        try {
            $phql = 'SELECT Posts.id as id_post, Posts.likes as likes, Posts.texto as texto, Posts.media as media, Posts.id_usuario as post_author, Posts.id_usuario_comparte as shared_by, UsuarioXSeguidores.id as user_follower_id FROM Posts LEFT JOIN UsuarioXSeguidores ON Posts.id_usuario = UsuarioXSeguidores.id_usuario WHERE UsuarioXSeguidores.id_seguidor = '.$id.' AND Posts.id_usuario = UsuarioXSeguidores.id_usuario OR Posts.id_usuario_comparte = UsuarioXSeguidores.id_usuario OR Posts.id_usuario = '.$id.' AND Posts.estatus = 1 GROUP BY id_post';

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];
                $i = 0;
                foreach ($resul as $res) {
                    $data[] = [
                        'post_id'   => $res->id_post,
                        'post_likes' => $res->likes,
                        'post_text' => $res->texto,
                        'post_media' => $res->media,
                        'post_author' => Usuarios::find($res->post_author),
                        'shared_by' => $res->shared_by != null ? Usuarios::find($res->shared_by) : null,
                        'user_x_followers' => UsuarioXSeguidores::find($res->user_follower_id)
                    ];
                    $phql = 'SELECT a.id as id_like, a.id_usuario as user_like, a.timestamp as time_like FROM UsuariosXLikes a WHERE a.id_post = '.$res->id_post;
                    $resul2 = $this->app->modelsManager->executeQuery($phql);
                    $userController = new UsuariosController($this->app);
                    if(count($resul2)>0){
                        foreach ($resul2 as $resu2){
                            $data[$i]['userLikes'][] = [
                                'id_like' => $resu2->id_like,
                                "id_user" => $userController->getOneNoResponse($resu2->user_like),
                                "time" => $resu2->time_like
                            ];
                        }
                    }else{
                        $data[$i]["userLikes"] = [];
                    }

                    $phql = 'SELECT b.id as id_comment, b.id_usuario as comment_user, b.texto as comment_text, b.timestamp as time_comment FROM Comentarios b WHERE b.id_post='. $res->id_post.' AND b.estatus = 1';
                    $resul3 = $this->app->modelsManager->executeQuery($phql);
                    $userController = new UsuariosController($this->app);
                    if(count($resul3)>0){
                        foreach ($resul3 as $resu3){
                            $data[$i]['userComments'][] = [
                                'id_comment' => $resu3->id_comment,
                                'comment_user' => $userController->getOneNoResponse($resu3->comment_user),
                                "text" => $resu3->comment_text,
                                "time" => $resu3->time_comment
                            ];
                        }
                    }else{
                        $data[$i]['userComments'] = [];
                    }
                    $i++;
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
            $phql = 'DELETE FROM Posts WHERE id = '.$id;

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
            $resul = new Posts();
            $data = (array)$data;
            if(isset($data["media"]) && !isset($data["isvideo"])){
                $media=$data["media"];
                $imgTitle=uniqid("img_");
                $actual_dir = realpath(__DIR__."/../../images/");
                $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                $img = Commons::base64ToImg($media, $actual_dir."/".$imgTitle.".jpg");
                $media=$imgTitle.".jpg";
                $data["media"] = $media;
            }else if(isset($data["media"]) && isset($data["isvideo"])){
                $media=$data["media"];
                $imgTitle=uniqid("vid_");
                $actual_dir = realpath(__DIR__."/../../videos/");
                $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                $img = Commons::base64ToImg($media, $actual_dir."/".$imgTitle.".mp4");
                $media=$imgTitle.".mp4";
                $this->generateThumbnail($imgTitle);
                $data["media"] = $media;
            }

            if($resul->create($data)) {
                $resul_tags = null;
                $id_post = $resul->id;
                if(isset($data["etiquetas"])){
                    foreach($data["etiquetas"] as $tag){
                        $tags = new PostXEtiquetas;
                        $tag->id_post = $id_post;
                        $tags->create((array)$tag);
                    }
                }
                Commons::response(201, array("mensaje" => $data) ,$this->app);
            }
        }catch(PDOException $e){
            echo $e;
            Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
        }
    }

    public function generateThumbnail($name)
    {
        exec('ffmpeg -i '.$name.'.mp4 -vf "thumbnail" -frames:v 1 '.$name.'.png');
        echo 'ffmpeg -i '.$name.'.mp4 -vf "thumbnail" -frames:v 1 '.$name.'.png';
    }

    public function rotateIfExif($filename)
    {
        $exifData = exif_read_data($filename);

        if(isset($exifData["Orientation"]))
        {
            if($exifData["Orientation"] != 1)
            {
                $input = imagecreatefromjpeg($filename);
                $rotatedImage = imagerotate($input,$this->getExifAngle($exifData["Orientation"]));
                imagejpeg($rotatedImage);
                imagedestroy($input); imagedestroy($rotatedImage);
            }
        }
    }

    private function getExifAngle($exifOrientation)
    {
        $newAngle = 0;
        switch ($exifOrientation) 
        {
            case 3:
                $newAngle = 180;
                break;
            case 6:
                $newAngle = 90;
                break;
            case 8:
                $newAngle = 270;
                break;
        }
        return $newAngle;
    }

    public function uploadVideo() {
        try{
            $data = [];
            $target_path = realpath(__DIR__."/../../images/");
            $data["file"] = $_FILES['file']['name'];
            $target_path = $target_path ."/". $_FILES['file']['name'];
            $data["path"]= $target_path;
            if (move_uploaded_file($_FILES['file']['name'], $target_path)) {
                    $data["name"] = realpath(__DIR__."/../../images/")."/".$_FILES['file']['name'];
            }
            $this->generateThumbnail($data["name"]);
            Commons::response(201, array("mensaje" => $data) ,$this->app);
        }catch(PDOException $e){
            Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
        }
    }

    public function update($id = null) {
        try {

            $data = $this->app->request->getJsonRawBody();
            $resul = Posts::find($id);
            
            if(count($resul)>0){
                if(isset($data->media)){
                    $media=$data->media;
                    $img=uniqid("img_");
                    Commons::base64ToImg($media, __DIR__."/../../images/".$img);
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
    
    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new PostsController($app);
        }
        return static::$instance;
    }

}

?>