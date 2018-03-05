<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class UsuariosXEquiposController
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

            $phql = 'SELECT * FROM UsuariosXEquipos'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_equipo' => Equipos::find($res->id_equipo),
                        'id_usuario' => Usuarios::find($res->id_usuario),
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

    public function getPlayersList()
    {

        try {
            $params = $this->app->request->getQuery();

            $phql = 'SELECT Usuarios.id as id_usuario, nombres, apellidos, id_posicion, email, username, foto_perfil, ranking, Usuarios.estatus as estatus_usuario, telefono, id_pais, UsuariosXEquipos.id as id_user_team,UsuariosXEquipos.id_equipo as team_id, UsuariosXEquipos.estatus as estatus_invitacion FROM Usuarios LEFT JOIN UsuariosXEquipos ON  (UsuariosXEquipos.id_usuario = Usuarios.id) WHERE Usuarios.id_pais = "'.$params['id_pais'].'" AND Usuarios.tipo_usuario = "1"'; 
            /**AND Usuarios.id_estado = "'.$params['id_estado'].'" AND Usuarios.id_ciudad = "'.$params['id_ciudad'].'" **/

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id_usuario' => $res->id_usuario,
                        'nombres' => $res->nombres,
                        'apellidos' => $res->apellidos,
                        'id_posicion' => $res->id_posicion,
                        'email' => $res->email,
                        'username' => $res->username,
                        'foto_perfil' => $res->foto_perfil,
                        'ranking' => $res->ranking,
                        'estatus_usuario' => $res->estatus_usuario,
                        'id_user_team' => $res->id_user_team,
                        'team_id' => $res->team_id,
                        'estatus_invitacion' => $res->estatus_invitacion
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
            $phql = 'SELECT * FROM UsuariosXEquipos WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_equipo' => Equipos::find($res->id_equipo),
                        'id_usuario' => Usuarios::find($res->id_usuario),
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

    public function delete($id = NULL)
    {
        try {
            $phql = 'DELETE FROM UsuariosXEquipos WHERE id = '.$id;

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
            $evento = new UsuariosXEquipos();
            $data = (array)$data;
            if($evento->create($data)) {
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
            $resul = UsuariosXEquipos::find($id);
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
            static::$instance = new UsuariosXEquiposController($app);
        }
        return static::$instance;
    }

}

?>
