<?php
	namespace olifant\constants
	{
		define(__NAMESPACE__ . '\NAMESPACE_SEPARATOR', '\\');
		define(__NAMESPACE__ . '\BACKEND_PATH'    , __DIR__);
		define(__NAMESPACE__ . '\FRONTEND_PATH'   , __DIR__ . '/../frontend');
		define(__NAMESPACE__ . '\APP_PATH'        , BACKEND_PATH . '/app');
		define(__NAMESPACE__ . '\ROUTE_PATH'      , BACKEND_PATH . '/route');
		define(__NAMESPACE__ . '\CONTROLLER_PATH' , BACKEND_PATH . '/controller');
		define(__NAMESPACE__ . '\MODEL_PATH'      , BACKEND_PATH . '/model');
		define(__NAMESPACE__ . '\VIEW_PATH'       , BACKEND_PATH . '/view');
		define(__NAMESPACE__ . '\SETUP_PATH'      , BACKEND_PATH . '/setup');
		define(__NAMESPACE__ . '\STORAGE_PATH'    , __DIR__ . '/../storage');
	}

	namespace
	{
		require_once \olifant\constants\BACKEND_PATH . '/app/kernel/autoload.php';
		spl_autoload_register('\olifant\kernel\Autoload::load');

		$configs = array(
			'settings',
			'enviroment',
			'general',
			'upgrade',
			'middlewares'
			//'events'
		);

		foreach($configs as $conf){
			require_once \olifant\constants\SETUP_PATH . DIRECTORY_SEPARATOR . $conf . '.php';
		}

		\olifant\kernel\Application::run();
	}
?>