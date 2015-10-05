<?php
	namespace olifant\kernel;
	
	use \olifant\http\Request;
	use \olifant\http\Response;
	use \olifant\http\ResponseBuilder;
	use \olifant\route\Router;
	use \olifant\middleware\MiddlewareManager;
	use \olifant\controller\FrontController;

	class Application
	{
		public static function run()
		{
			$events   = EventListener::getInstance();
			//$events->trigger('app.run');

			$request  = new Request($_SERVER['QUERY_PATH']);
			$response = new Response();

			$router = new Router($request);
			$callable = $router->route();

			$input  = $request->build();
			$output = $response->prepare();

			MiddlewareManager::getInstance()->before($input,$output,$callable);

			$return = FrontController::getInstance()
				->setController($callable->controller)
				->setAction($callable->action)
				->setParams($input,$output)
				->exec();

			if($return instanceof ResponseBuilder){
				$output = $return;
			}else{
				$output->body = $return;
			}

			MiddlewareManager::getInstance()->after($input,$output,$callable);

			$response->send($output);

			//$events->trigger('app.done');
		}
	}
?>