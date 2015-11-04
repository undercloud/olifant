<?php
	call_user_func(function(){
		if('CLI' != $_SERVER['REQUEST_METHOD']){
			set_error_handler('\olifant\kernel\ErrorHandler::handleError');
		}

		//switch(\olifant\constants\APP_DEV_MODE){
			//case \olifant\constants\DEBUG:
				$error_reporting = E_ALL | E_STRICT;
				$error_mode = 'On';
			//break;

			/*case \olifant\constants\RELEASE:
				$error_reporting = 0;
				$error_mode = 'Off';
			break;*/
		//}

		error_reporting($error_reporting);
		ini_set('display_errors', $error_mode);
		ini_set('display_startup_errors',$error_mode);

		if($_SERVER['REQUEST_METHOD'] == 'CLI')
			ini_set('html_errors','Off');

		if(true === \olifant\Settings::get('system.errlog',false)){
			ini_set('log_errors','On');
			ini_set('error_log',\olifant\constants\STORAGE_PATH . '/log/error/' . date('Y-m') . '.error.log');
			ini_set('log_errors_max_len',10 * 1024);
		}
	});

	call_user_func(function(){
		date_default_timezone_set('UTC');
		ini_set('memory_limit',\olifant\Settings::get('system.memory_limit','128M'));
		set_time_limit(\olifant\Settings::get('system.time_limit',30));
	});

	call_user_func(function(){
		$obd = array(
			\olifant\constants\BACKEND_PATH,
			\olifant\constants\FRONTEND_PATH,
			\olifant\constants\STORAGE_PATH
		);

		ini_set('open_basedir',implode(PATH_SEPARATOR,$obd));
	});

	call_user_func(function(){
		ini_set('session.name', 'UNIQSESSID');
		ini_set('session.cookie_lifetime',30 * 3600);
		ini_set('session.cookie_httponly',1);
		ini_set('session.gc_divisor', 100);
		ini_set('session.gc_maxlifetime', 40);
		ini_set('session.gc_probability', 100);
		//session_start();
		//new \core\utils\ModelSessionHandler();
	});

	// php souce highlight
	call_user_func(function(){
		ini_set('highlight.comment','#969896');		 
		ini_set('highlight.default','#395063');	 
		ini_set('highlight.html'   ,'#888888');		 
		ini_set('highlight.keyword','#2d93c6');	 
		ini_set('highlight.string' ,'#05ad97');
	});
?>