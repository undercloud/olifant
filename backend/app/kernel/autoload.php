<?php
	namespace olifant\kernel;

	class Autoload
	{
		private static function normalize($name)
		{
			$exclude = array(
				'olifant/settings'                     => 'app/kernel/settings',
				'olifant/route/router'                 => 'app/route/router',
				'olifant/route/routebase'              => 'app/route/base',
				'olifant/controller/frontcontroller'   => 'app/controller/frontcontroller',
				'olifant/controller/controllerbase'    => 'app/controller/base',
				'olifant/controller/controllerclosure' => 'app/controller/closure',
				'olifant/middleware/middlewaremanager' => 'app/middleware/manager',
				'olifant/middleware/middlewarebase'    => 'app/middleware/base',
				'olifant/cli'                          => 'app/kernel/cli',
				'olifant/benchmark'                    => 'app/kernel/benchmark'
			);
			
			if(array_key_exists($name, $exclude)){
				return $exclude[$name];
			}else{
				$path  = explode(\DIRECTORY_SEPARATOR, $name);
				$class = end($path);

				if($path[0] === 'olifant'){
					if(in_array($path[1], array('route', 'controller', 'model', 'middleware'))){
						unset($path[0]);

						$class = str_replace($path[1], '', $class);
						array_pop($path);
						array_push($path, $class);
					}else{
						$path[0] = 'app';
					}
				}	

				return implode(\DIRECTORY_SEPARATOR, $path);
			}
		}

		public static function load($name)
		{
			if(\olifant\constants\NAMESPACE_SEPARATOR != \DIRECTORY_SEPARATOR)
				$name = str_replace(\olifant\constants\NAMESPACE_SEPARATOR, \DIRECTORY_SEPARATOR, $name);

			$name = strtolower($name);
			$name = self::normalize($name);

			$fullpath = \olifant\constants\BACKEND_PATH . \DIRECTORY_SEPARATOR . $name . '.php';
			
			if(file_exists($fullpath))
				require_once $fullpath;
		}
	}
?>