<?php
	namespace olifant\kernel;

	class Autoload
	{
		private static function normalize($name)
		{
			$exclude = array(
				'olifant/route/router' => 'app/route/router',
				'olifant/route/routebase' => 'app/route/base',
				'olifant/controller/frontcontroller' => 'app/controller/frontcontroller',
				'olifant/controller/controllerbase' => 'app/controller/base'
			);

			if(array_key_exists($name,$exclude)){
				return $exclude[$name];
			}else{
				$path  = explode(\DIRECTORY_SEPARATOR,$name);
				$class = end($path);

				if($path[0] == 'olifant'){
					if(in_array($path[1],array('route','controller'))){
						unset($path[0]);

						$class = str_replace($path[1],'',$class);
						array_pop($path);
						array_push($path,$class);
					}else{
						$path[0] = 'app';
					}
				}	

				return implode(\DIRECTORY_SEPARATOR,$path);
			}
		}

		public static function load($name)
		{
			if(\app\conf\NAMESPACE_SEPARATOR != \DIRECTORY_SEPARATOR)
				$name = str_replace(\app\conf\NAMESPACE_SEPARATOR, \DIRECTORY_SEPARATOR, $name);

			$name = strtolower($name);
			$name = self::normalize($name);

			$fullpath = \app\conf\BACKEND_PATH . \DIRECTORY_SEPARATOR . $name . '.php';
			
			if(file_exists($fullpath))
				require_once $fullpath;
		}
	}
?>