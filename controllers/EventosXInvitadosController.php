<?php

use Phalcon\Db\Adapter\Pdo\Sqlite;

class EventosXInvitadosController
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

            $phql = 'SELECT * FROM EventosXInvitados'.$filtros;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_evento' => Eventos::find($res->id_evento),
                        'id_jugador_invitado' => Usuarios::find($res->id_jugador_invitado),
                        'respuesta' => $res->respuesta,
                        'timestamp' => $res->timestamp,
                        'aprobado' => $res->aprobado,
                        'id_equipo_invitado' => Equipos::find($res->id_equipo_invitado)
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

    public function getNextGames(){
        try {
            $params = $this->app->request->getQuery();

            $phql = 'SELECT a.fecha_partido, a.id as id_evento, b.id as id_evento_x_invitado FROM Eventos a, EventosXInvitados b WHERE (a.fecha_partido = now() OR a.fecha_partido > now()) AND a.estatus = 1 AND b.id_jugador_invitado = '.$params["id_usuario"].' AND a.id_tipo_evento = 2 GROUP BY a.id'; 

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];
                $i = 0;
                foreach ($resul as $res) {
                    $data[] = [
                        'id_evento_x_invitado'   => $res->id_evento_x_invitado,
                        'id_evento' => Eventos::find($res->id_evento)
                    ];

                    $sql = "SELECT id_jugador_invitado, respuesta, id FROM EventosXInvitados WHERE id_evento =".$res->id_evento;
                    $resul2 = $this->app->modelsManager->executeQuery($sql);
                    if(count($resul2)>0){
                        $o = 0;
                        foreach($resul2 as $res2){
                            $data[$i]['invitados'][] = Usuarios::find($res2->id_jugador_invitado)[0];
                            unset($data[$i]['invitados'][$o]->password);
                            unset($data[$i]['invitados'][$o]->token);
                            $data[$i]['invitados'][$o] = (array)$data[$i]['invitados'][$o];
                            $data[$i]['invitados'][$o]['respuesta'] = $res2->respuesta;
                            $data[$i]['invitados'][$o]['id_evento_invitados'] = $res2->id;
                            $o++;
                        }
                    }
                    $i++;
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

    public function getNextTorneos(){
        try {
            $params = $this->app->request->getQuery();

            $phql = 'SELECT a.fecha_partido, a.id as id_evento, b.id as id_evento_x_invitado FROM Eventos a, EventosXInvitados b WHERE (a.fecha_partido = now() OR a.fecha_partido > now()) AND a.estatus = 1 AND b.id_equipo_invitado IN ( SELECT c.id_equipo FROM UsuariosXEquipos c WHERE c.id_usuario ='.$params["id_usuario"].' ) AND a.id_tipo_evento = 1 GROUP BY a.id'; 

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];
                $i = 0;
                foreach ($resul as $res) {
                    $data[] = [
                        'id_evento_x_invitado'   => $res->id_evento_x_invitado,
                        'id_evento' => Eventos::find($res->id_evento)
                    ];

                    $sql = "SELECT id_equipo_invitado, respuesta, id FROM EventosXInvitados WHERE id_evento =".$res->id_evento;
                    $resul2 = $this->app->modelsManager->executeQuery($sql);
                    if(count($resul2)>0){
                        $o = 0;
                        foreach($resul2 as $res2){
                            $data[$i]['invitados'][] = Equipos::find($res2->id_equipo_invitado)[0];
                            $data[$i]['invitados'][$o] = (array)$data[$i]['invitados'][$o];
                            $data[$i]['invitados'][$o]['respuesta'] = $res2->respuesta;
                            $data[$i]['invitados'][$o]['id_evento_invitados'] = $res2->id;
                            $o++;
                        }
                    }
                    $i++;
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

            $phql = 'SELECT Usuarios.id as id_usuario, nombres, apellidos, id_posicion, email, username, foto_perfil, ranking, Usuarios.estatus as estatus_usuario, telefono, id_pais, EventosXInvitados.id as id_evento,EventosXInvitados.id_equipo_invitado as equipo_invitado, EventosXInvitados.respuesta as respuesta, EventosXInvitados.id_jugador_invitado as jugador_invitado, EventosXInvitados.timestamp as fecha_invitacion, EventosXInvitados.aprobado as aprobado FROM Usuarios LEFT JOIN EventosXInvitados ON  (EventosXInvitados.id_jugador_invitado = Usuarios.id) WHERE Usuarios.id_pais = "'.$params['id_pais'].'" AND Usuarios.tipo_usuario = "1" AND Usuarios.tipo_usuario = "1" AND (Usuarios.id NOT IN (SELECT EventosXInvitados.id_jugador_invitado FROM EventosXInvitados WHERE EventosXInvitados.id_evento = '.$params['evento'].')) GROUP BY Usuarios.id ORDER BY RAND() LIMIT '.$params["limit"]; 
            /**AND Usuarios.id_estado = "'.$params['id_estado'].'" AND Usuarios.id_ciudad = "'.$params['id_ciudad'].'" **/
            //var_dump($phql);
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
                        'id_evento' => $res->id_evento,
                        'equipo_invitado' => $res->equipo_invitado,
                        'respuesta' => $res->respuesta,
                        'jugador_invitado' => $res->jugador_invitado,
                        'fecha_invitacion' => $res->fecha_invitacion,
                        'aprobado' => $res->aprobado
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

    public function getTeamsList()
    {

        try {
            $params = $this->app->request->getQuery();

            $phql = 'SELECT Equipos.id as id_equipo, nombre, descripcion, imagen_equipo, id_admin, estatus, EventosXInvitados.id as id_evento,EventosXInvitados.id_equipo_invitado as equipo_invitado, EventosXInvitados.respuesta as respuesta, EventosXInvitados.timestamp as fecha_invitacion, EventosXInvitados.aprobado as aprobado FROM Equipos LEFT JOIN EventosXInvitados ON  (EventosXInvitados.id_equipo_invitado = Equipos.id) WHERE Equipos.id_pais = "'.$params['id_pais'].'" ORDER BY RAND() LIMIT '.$params["limit"]; 
            /**AND Usuarios.id_estado = "'.$params['id_estado'].'" AND Usuarios.id_ciudad = "'.$params['id_ciudad'].'" **/

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id_equipo' => $res->id_equipo,
                        'nombre' => $res->nombre,
                        'descripcion' => $res->descripcion,
                        'imagen_equipo' => $res->imagen_equipo,
                        'id_admin' => Usuarios::find($res->id_admin),
                        'estatus_equipo' => $res->estatus,
                        'id_evento' => $res->id_evento,
                        'equipo_invitado' => $res->equipo_invitado,
                        'id_evento' => $res->id_evento,
                        'equipo_invitado' => $res->equipo_invitado,
                        'respuesta' => $res->respuesta,
                        'fecha_invitacion' => $res->fecha_invitacion,
                        'aprobado' => $res->aprobado
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
            $phql = 'SELECT * FROM EventosXInvitados WHERE id = '.$id;

            $resul = $this->app->modelsManager->executeQuery($phql);
            if(count($resul)>0){
                $data = [];

                foreach ($resul as $res) {
                    $data[] = [
                        'id'   => $res->id,
                        'id_evento' => Eventos::find($res->id_evento),
                        'id_jugador_invitado' => Usuarios::find($res->id_jugador_invitado),
                        'respuesta' => $res->respuesta,
                        'timestamp' => $res->timestamp,
                        'aprobado' => $res->aprobado,
                        'id_equipo_invitado' => Equipos::find($res->id_equipo_invitado)
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
            $phql = 'DELETE FROM EventosXInvitados WHERE id = '.$id;

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
            $resul = new EventosXInvitados();
            $data = (array)$data;
            if($resul->create($data)) {
                $data["id"] = $resul->id;
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
            $resul = EventosXInvitados::find($id);
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
            static::$instance = new EventosXInvitadosController($app);
        }
        return static::$instance;
    }

}

?>