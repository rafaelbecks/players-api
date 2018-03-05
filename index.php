<?php 

/*
 +------------------------------------------------------------------------+
 | FootballAPP API REST                                                      |
 +------------------------------------------------------------------------+
*/

	/* Se registran los directorios y se cargan las clases de la api */

	class futbol
	{

	    private static $instance = NULL;
	    public $app = NULL;
	    public $loader = NULL;


	    public function __construct()
	    {
	        $this->init();
	    }
	    
		private function init()
		{
			$this->loader = new \Phalcon\Loader();
			$this->loader->registerDirs(array(
			    'controllers',
			    'models',
			    'routes',
			    'commons',
			    'auth'
			));

			$this->loader->register();		
		
			$this->bootstrap();
		}


		private function bootstrap()
		{
			$di = Commons::getDI();

			$this->app = new \Phalcon\Mvc\Micro($di);
			
			$this->app->get('/', function() {
				echo "Football App API REST";
			});

			$this->app->notFound(function () {
			    $this->app->response->setStatusCode(404, "Not Found")->sendHeaders();
			    echo 'La ruta no ha sido encontrada';
			});

			$this->loadRoutes();

			$this->app->handle();
		}


		private function loadRoutes()
		{

			$usersController = UsersController::getInstance($this->app);
			UsersRouter::getInstance($usersController);

			$paisesController = PaisesController::getInstance($this->app);
			PaisesRouter::getInstance($paisesController);

			$estadosController = EstadosController::getInstance($this->app);
			EstadosRouter::getInstance($estadosController);

			$ciudadesController = CiudadesController::getInstance($this->app);
			CiudadesRouter::getInstance($ciudadesController);

			$eventosController = EventosController::getInstance($this->app);
			EventosRouter::getInstance($eventosController);

			$empresasServiciosController = EmpresasServiciosController::getInstance($this->app);
			EmpresasServiciosRouter::getInstance($empresasServiciosController);

			$equiposController = EquiposController::getInstance($this->app);
			EquiposRouter::getInstance($equiposController);

			$mensajesController = MensajesController::getInstance($this->app);
			MensajesRouter::getInstance($mensajesController);

			$comentariosController = ComentariosController::getInstance($this->app);
			ComentariosRouter::getInstance($comentariosController);

			$eventosXInvitadosController = EventosXInvitadosController::getInstance($this->app);
			EventosXInvitadosRouter::getInstance($eventosXInvitadosController);

			$postsController = PostsController::getInstance($this->app);
			PostsRouter::getInstance($postsController);

			$postXEtiquetasController = PostXEtiquetasController::getInstance($this->app);
			PostXEtiquetasRouter::getInstance($postXEtiquetasController);

			$posicionesController = PosicionesController::getInstance($this->app);
			PosicionesRouter::getInstance($posicionesController);

			$tipoEventoController = TipoEventoController::getInstance($this->app);
			TipoEventoRouter::getInstance($tipoEventoController);

			$tipoUsuarioController = TipoUsuarioController::getInstance($this->app);
			TipoUsuarioRouter::getInstance($tipoUsuarioController);

			$usuariosController = UsuariosController::getInstance($this->app);
			UsuariosRouter::getInstance($usuariosController);

			$usuariosXEquiposController = UsuariosXEquiposController::getInstance($this->app);
			UsuariosXEquiposRouter::getInstance($usuariosXEquiposController);

			$usuariosXLikesController = UsuariosXLikesController::getInstance($this->app);
			UsuariosXLikesRouter::getInstance($usuariosXLikesController);

			$usuarioXSeguidoresController = UsuarioXSeguidoresController::getInstance($this->app);
			UsuarioXSeguidoresRouter::getInstance($usuarioXSeguidoresController);
			$this->loadAuth($usuariosController);

			$notificacionesController = NotificacionesController::getInstance($this->app);
			NotificacionesRouter::getInstance($notificacionesController);

		}

		private function loadAuth($usersController)
		{
			Auth::getInstance()->middlewareAuth($usersController,$this->app);
		}

	    public static function getInstance(){
	        if(is_null(static::$instance)){
	            static::$instance = new futbol();
	        }
	        return static::$instance;
	    }

	}

	//Comentar para producción
	 ini_set('display_errors', 'On');
	 futbol::getInstance();


?>
