<?php
	namespace app\conf {
		define(__NAMESPACE__ . '\DEBUG'   , 0x000);
		define(__NAMESPACE__ . '\RELEASE' , 0xFFF);
		define(__NAMESPACE__ . '\APP_DEV_MODE', DEBUG); // DEBUG | RELEASE
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

	namespace {
		require_once \app\conf\BACKEND_PATH . '/app/kernel/autoload.php';
		spl_autoload_register('\olifant\kernel\Autoload::load');

		$configs = array(
			'general',
			'enviroment',
			'upgrade',
			//'events'
		);

		foreach($configs as $conf){
			require_once \app\conf\SETUP_PATH . DIRECTORY_SEPARATOR . $conf . '.php';
		}

		\olifant\kernel\Application::run();
	}
?>