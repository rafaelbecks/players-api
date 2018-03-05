<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class UsuariosController
{

    public $app = NULL;
    public static $instance = NULL;

    public function __construct ($app)
    {
        $this->app = $app;
    }

    public function getAll($filter=null)
    {
        
        try {
            if($filter!=null){
                $filtros = Commons::filterValidator($filter);
            }else{
                $filtros = Commons::filterValidator($this->app->request->getQuery());
            }
            
            $phql = 'SELECT * FROM Usuarios'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "id_pais" => Paises::find($res->id_pais),
                        "fecha_nacimiento" => $res->fecha_nacimiento,
                        "id_ciudad" => Ciudades::find($res->id_ciudad),
                        "peso" => $res->peso,
                        "talla" => $res->talla,
                        "id_posicion" => $res->id_posicion,
                        "email" => $res->email,
                        "username" => $res->username,
                        "ranking" => $res->ranking,
                        "estatus" => $res->estatus,
                        "sexo" => $res->sexo,
                        "id_estado" => Estados::find($res->id_estado),
                        "telefono" => $res->telefono,
                        "biografia" => $res->biografia,
                        "tipo_usuario" => TipoUsuario::find($res->tipo_usuario)
                    ];
                }
                if($filter){
                    return $data;
                }else{
                    Commons::response(200 ,array("mensaje" => $data), $this->app);
                }
            }else{
                if($filter){
                    return null;
                }else{
                    Commons::response(404, array("mensaje" => "Aún no hay registros"), $this->app);
                }
            }

        } catch (PDOException $e) 
        {
            Commons::response(500,array("mensaje" => "Ocurrió un error al listar los registros ".$e->getMessage()),$this->app);                                
        }
    }

    public function search() {
        try {
            $params = $this->app->request->getQuery();
            
            $phql = 'SELECT id, nombres, apellidos, foto_perfil, username FROM Usuarios WHERE estatus = 1 AND tipo_usuario = 1 AND (username LIKE "%'.$params['username'].'%" OR nombres LIKE "%'.$params['username'].'%")';
            
            $resul = $this->app->modelsManager->executeQuery($phql);
            
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "username" => $res->username
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
            $phql = 'SELECT * FROM Usuarios WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "id_pais" => Paises::find($res->id_pais),
                        "fecha_nacimiento" => $res->fecha_nacimiento,
                        "id_ciudad" => Ciudades::find($res->id_ciudad),
                        "peso" => $res->peso,
                        "talla" => $res->talla,
                        "id_posicion" => $res->id_posicion,
                        "email" => $res->email,
                        "username" => $res->username,
                        "ranking" => $res->ranking,
                        "estatus" => $res->estatus,
                        "sexo" => $res->sexo,
                        "id_estado" => Estados::find($res->id_estado),
                        "telefono" => $res->telefono,
                        "biografia" => $res->biografia,
                        "tipo_usuario" => TipoUsuario::find($res->tipo_usuario)
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

    public function getOneNoResponse($id = NULL)
    {
        try {
            $phql = 'SELECT * FROM Usuarios WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "id_pais" => Paises::find($res->id_pais),
                        "fecha_nacimiento" => $res->fecha_nacimiento,
                        "id_ciudad" => Ciudades::find($res->id_ciudad),
                        "peso" => $res->peso,
                        "talla" => $res->talla,
                        "id_posicion" => $res->id_posicion,
                        "email" => $res->email,
                        "username" => $res->username,
                        "ranking" => $res->ranking,
                        "estatus" => $res->estatus,
                        "sexo" => $res->sexo,
                        "id_estado" => Estados::find($res->id_estado),
                        "telefono" => $res->telefono,
                        "biografia" => $res->biografia,
                        "tipo_usuario" => TipoUsuario::find($res->tipo_usuario)
                    ];
                }

                return $data;
            }else{
                return null;
            }

        } catch (PDOException $e) 
        {
            return null;                                
        }       
    }

    public function delete($id = NULL)
    {
        try {
            $phql = 'DELETE FROM Usuarios WHERE id = '.$id;

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
            $evento = new Usuarios();
            $data = (array)$data;
            $data["password"] = sha1($data["password"]);
            $data["token"] = sha1($data["email"].$data["password"]);
            try{
                if($evento->create($data)) {
                    Commons::response(201, array("mensaje" => $data) ,$this->app);
                }else{
                    Commons::response(500, array("mensaje" => (string)$evento->getMessages()[0]),$this->app);
                }
            }catch(PDOException $e){
                Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
            }
        }catch(PDOException $e){
            Commons::response(500,array("mensaje" => "Ocurrió un error al registrar la data".$e->getMessage()),$this->app);
        }
    }

    public function update($id = null) {
        try {

            $data = $this->app->request->getJsonRawBody();
            $resul = Usuarios::find($id);
            $data = (array)$data;
            if(count($resul)>0){
                if(isset($data["foto_perfil"])){
                    $img_url = parse_ini_file(__DIR__."/../db/Config.ini");
                    $file_name = uniqid("pf")."_".$resul[0]->id.".jpg";
                    $output = $img_url["absolute_url"].$file_name;
                    $img = Commons::base64ToImg($data["foto_perfil"],$output);
                    $data["foto_perfil"] = $file_name;
                }
                if($resul->update($data)) {
                   $this->getOne($id);
                } 
            }else{
                Commons::response(404, array("mensaje" => "El registro no existe"), $this->app);
            }
        }catch(PDOException $e){
            Commons::response(500,array("mensaje" => "Ocurrió un error al editar la data".$e->getMessage()),$this->app);
        }
    }

    public function login()
    {

        $credentials = $this->app->request->getJsonRawBody();
        try {

            $password = sha1($credentials->password);

            $phql = "SELECT * FROM Usuarios WHERE
            (username = '$credentials->login' AND password = '$password') OR
            (email = '$credentials->login' AND password = '$password')";

            $resul = $this->app->modelsManager->executeQuery($phql);

            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "id_pais" => $res->id_pais,
                        "fecha_nacimiento" => $res->fecha_nacimiento,
                        "id_ciudad" => $res->id_ciudad,
                        "peso" => $res->peso,
                        "talla" => $res->talla,
                        "id_posicion" => $res->id_posicion,
                        "email" => $res->email,
                        "username" => $res->username,
                        "ranking" => $res->ranking,
                        "estatus" => $res->estatus,
                        "sexo" => $res->sexo,
                        "id_estado" => $res->id_estado,
                        "telefono" => $res->telefono,
                        "biografia" => $res->biografia,
                        "tipo_usuario" => $res->tipo_usuario,
                        "token" => $res->token
                    ];
                }
                Commons::response(200, array("resultado" => "OK", "usuario" => $data),$this->app);
            }else
            {
                Commons::response(401,array("resultado" => "ERROR", "mensaje" => "Credenciales Incorrectas"),$this->app);
            }
            
        } catch (Exception $e) {
            Commons::response(500,array("mensaje" => "Ocurrió un error al iniciar sesión ".$e->getMessage()),$this->app);
        }
    }

    public function socialLogin() {
        $data = $this->app->request->getJsonRawBody();
        $filter = array("email"=>$data->email);
        $users = $this->getAll($filter);
        if(count($users)>0){
            $this->loginSocial($users[0]);
        }else{
            try{
                $user = new Usuarios();
                $data = (array)$data;
                $data["password"] = sha1($data["password"]);
                $data["token"] = sha1($data["email"].$data["password"]);
                $data["tipo_usuario"] = 1;
                if($user->create($data)){
                    $this->loginSocial($data);
                    //Commons::response(200, array("mensaje" => "usuario nuevo"), $this->app); 
                }
            }catch (Exception $e) {
                Commons::response(500,array("mensaje" => "Ocurrió un error al iniciar sesión ".$e->getMessage()),$this->app);
            }
        }
    }

    public function loginSocial($user)
    {
        $email = $user['email'];
        $login = $user['username'];
        try {
            $phql = "SELECT * FROM Usuarios WHERE
            username = '$login' AND email = '$email'";
            $resul = $this->app->modelsManager->executeQuery($phql);

            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        "nombres" => $res->nombres,
                        "apellidos" => $res->apellidos,
                        "foto_perfil" => $res->foto_perfil,
                        "id_pais" => $res->id_pais,
                        "fecha_nacimiento" => $res->fecha_nacimiento,
                        "id_ciudad" => $res->id_ciudad,
                        "peso" => $res->peso,
                        "talla" => $res->talla,
                        "id_posicion" => $res->id_posicion,
                        "email" => $res->email,
                        "username" => $res->username,
                        "ranking" => $res->ranking,
                        "estatus" => $res->estatus,
                        "sexo" => $res->sexo,
                        "id_estado" => $res->id_estado,
                        "telefono" => $res->telefono,
                        "biografia" => $res->biografia,
                        "tipo_usuario" => $res->tipo_usuario,
                        "token" => $res->token
                    ];
                }
                Commons::response(200, array("resultado" => "OK", "usuario" => $data),$this->app);
            }else
            {
                Commons::response(401,array("resultado" => "ERROR", "mensaje" => "Credenciales Incorrectas"),$this->app);
            }
            
        } catch (Exception $e) {
            Commons::response(500,array("mensaje" => "Ocurrió un error al iniciar sesión ".$e->getMessage()),$this->app);
        }
    }

    public function auth($token)
    {
        try {
            return Usuarios::find("token = '$token'");
        } catch (Exception $e) {
            
        }

    }
    
    public static function getInstance($app)
    {
        if(is_null(static::$instance)){
            static::$instance = new UsuariosController($app);
        }
        return static::$instance;
    }

}

?>