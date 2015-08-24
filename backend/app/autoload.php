<?php
	namespace app;
	use app;

	class Autoload
	{
		private static function normalize($name)
		{
			$path  = explode(\DIRECTORY_SEPARATOR,$name);
			$ns    = reset($path); 
			$class = end($path);

			if(in_array($ns,array('controller','model','route'))){
				$class = str_replace($ns,'',$class);
				array_pop($path);
				array_push($path,$class);
			}

			return implode(\DIRECTORY_SEPARATOR,$path);
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