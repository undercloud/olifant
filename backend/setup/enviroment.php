<?php
	call_user_func(function(){
		if((!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)))){
			$_SERVER['REQUEST_METHOD'] = 'CLI';
			$_SERVER['REQUEST_URI']    = $_SERVER['argv'][1];

			$pos = strpos($_SERVER['REQUEST_URI'],'?');
			if(false !== $pos){
				$_SERVER['QUERY_STRING'] = substr($_SERVER['REQUEST_URI'],$pos + 1);
				parse_str($_SERVER['QUERY_STRING'],$_REQUEST);
			}

			$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../backend';
		}

		$_SERVER['QUERY_PATH'] = $_SERVER['REQUEST_URI'];
		$pos = strpos($_SERVER['QUERY_PATH'],'?');
		if($pos !== false)
			$_SERVER['QUERY_PATH'] = substr($_SERVER['QUERY_PATH'],0,$pos);
	});

	/*call_user_func(function(){
		$_SERVER['PHP_INPUT'] = file_get_contents('php://input');
		if(in_array(strtolower($_SERVER['REQUEST_METHOD']),array('put','delete'))){
			parse_str($_SERVER['PHP_INPUT'],$_REQUEST);
		}
	});*/

	call_user_func(function(){
		if(function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc()){
			function stripslashes_gpc(&$value){
				$value = stripslashes($value);
			}

			array_walk_recursive($_GET, 'stripslashes_gpc');
			array_walk_recursive($_POST, 'stripslashes_gpc');
			array_walk_recursive($_COOKIE, 'stripslashes_gpc');
			array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}
	});
?>