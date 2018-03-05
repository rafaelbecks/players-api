<?php 

Class Auth{

	private static $instance = NULL;

	private $forbiddenRoutes = ["/comentarios", "/equipos", "/eventos", "/posts", "/post_etiquetas", "/eventos_invitados", "/tipo_evento", "/usuarios_equipos", "/usuarios_likes", "/usuarios_seguidores"];

	/* Función Middleware para manejar la autenticación */

	public function middlewareAuth($usersController,$app){

		$app->before(function() use ($app,$usersController) {

			$validUser = false;

			$token = $app->request->getHeader("token");

			$route=$app->request->getURI();

			$method=$app->request->getMethod();

			if($method == "OPTIONS")
			{
				Commons::response(200,array("mensaje" => "OK"),$app);
				return true;
			}
			if(!Commons::inArrayContains($this->forbiddenRoutes,$route))
			{
				return true;
			}else
			{

				if(!empty($token))
				{

					$auth = $usersController->auth($token);

					if(count($auth)>0)
					{
						return true;
					}else
					{
						Commons::response(401, array("mensaje" => "Credenciales incorrectas"),$app);
						return $auth;
					}



				}else
				{
					Commons::response(401, array("mensaje" => "Debe incluir token en el header"),$app);
					return false;
				}
				
			}

		});
	}

	public static function getInstance(){
		if (is_null(static::$instance)) {
    	  	static::$instance = new Auth();
  	    }
        return static::$instance;
    }

}
?>