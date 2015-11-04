<?php
	namespace olifant\controller;

	use olifant\exceptions\AppException;

	class ControllerClosure extends ControllerBase
	{
		private static $index = 0;
		private static $fn    = array();

		public static function bind($closure, $uniqfn = null)
		{
			if(false == \is_closure($closure)){
				throw new AppException('Invalid closure');
			}

			if(null === $uniqfn){
				$uniqfn = 'closureMethod' . (++self::$index);
			}else{
				if(array_key_exists($uniqfn, self::$fn)){
					throw new AppException('Method ' . $uniqfn . ' already exists in ' . __CLASS__);
				}
			}
			
			self::$fn[$uniqfn] = $closure;

			return $uniqfn;
		}

		public function __call($uniqfn, array $arguments)
		{
			if(isset(self::$fn[$uniqfn])){
				return call_user_func_array(self::$fn[$uniqfn], $arguments);
			}else{
				throw new AppException('Method ' . $uniqfn . ' is not defined in ' . __CLASS__);
			}
		}
	}
?>