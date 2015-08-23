<?php
	namespace controller;

	class ControllerClosure extends ControllerBase
	{
		private static $index = 0;
		private static $fn = array();

		public static function bind($closure)
		{
			$uniqfn = 'method' . (++self::$index);
			self::$fn[$uniqfn] = $closure;

			return $uniqfn;
		}

		public function __call($uniqfn,$args)
		{
			if(isset(self::$fn[$uniqfn])){
				return call_user_func_array(self::$fn[$uniqfn],$args);
			}
		}
	}
?>