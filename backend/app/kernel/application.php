<?php
	namespace olifant\kernel;
	
	use \olifant\http\Request;
	use \olifant\http\Response;
	use \olifant\route\Router;
	use \olifant\middleware\MiddlewareManager;
	use \olifant\controller\FrontController;

	class Application
	{
		public static function run()
		{
			$events   = EventListener::getInstance();
			//$events->trigger('app.run');

			$request  = new Request();
			$response = new Response();

			$router = new Router($request);
			$callable = $router->route();

			$input  = $request->build();
			$output = $response->prepare();

			MiddlewareManager::getInstance()->before($input,$output,$callable);

			$output = FrontController::getInstance()
				->setController($callable->controller)
				->setAction($callable->action)
				->setParams($input,$output)
				->exec();

			MiddlewareManager::getInstance()->after($input,$output,$callable);

			if(is_object($output)){
				$response->send($output);
			}

			//$events->trigger('app.done');
		}
	}
?>