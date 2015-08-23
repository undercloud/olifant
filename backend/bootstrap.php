<?php
	namespace app\conf {
		define('DEBUG'   , 0x000);
		define('RELEASE' , 0xFFF);
		define('DEV_MODE', DEBUG); // DEBUG | RELEASE
		define('NAMESPACE_SEPARATOR', '\\');
		define('BACKEND_PATH'    , __DIR__);
		define('FRONTEND_PATH'   , __DIR__ . '/../frontend');
		define('APP_PATH'        , BACKEND_PATH . '/app');
		define('ROUTE_PATH'      , BACKEND_PATH . '/route');
		define('CONTROLLER_PATH' , BACKEND_PATH . '/controller');
		define('MODEL_PATH'      , BACKEND_PATH . '/model');
		define('VIEW_PATH'       , BACKEND_PATH . '/view');
		define('SETUP_PATH'      , BACKEND_PATH . '/setup');
	}

	namespace {
		require_once BACKEND_PATH . '/app/autoload.php';
		spl_autoload_register('\app\Autoload::load');

		$configs = array(
			'general',
			'enviroment',
			'upgrade',
			'events'
		);

		foreach($configs as $conf){
			require_once SETUP_PATH . DIRECTORY_SEPARATOR . $conf . '.php';
		}

		\app\Application::run();
	}
?>