<?php
	call_user_func(function(){
		switch(\app\conf\APP_DEV_MODE){
			case \app\conf\DEBUG:
				$error_reporting = E_ALL | E_STRICT;
				$error_mode = 'On';
			break;

			case \app\conf\RELEASE:
				$error_reporting = 0;
				$error_mode = 'Off';
			break;
		}

		error_reporting($error_reporting);
		ini_set('display_errors', $error_mode);
		ini_set('display_startup_errors',$error_mode);
		ini_set('xdebug.default_enable', $error_mode);

		if($_SERVER['REQUEST_METHOD'] == 'CLI')
			ini_set('html_errors','Off');

		ini_set('log_errors','On');
		ini_set('error_log',\app\conf\APP_PATH . '/com/error.log');
	});

	call_user_func(function(){
		date_default_timezone_set('UTC');
		set_time_limit(30);
	});

	call_user_func(function(){
		$obd = array(
			\app\conf\BACKEND_PATH,
			\app\conf\FRONTEND_PATH,
			\app\conf\STORAGE_PATH
		);

		ini_set('open_basedir',implode(':',$obd));
		//ini_set('upload_tmp_dir')
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