<?php
	namespace olifant\http;

	use \olifant\exceptions\AppException;

	class CookieWriter
	{
		private static $queue = array();

		public static function set(array $c)
		{
			if(false == isset($c['name'])){
				throw new AppException('Invalid cookie params');
			}

			if(false == isset($c['value']))    $c['value']    = '';
			if(false == isset($c['expire']))   $c['expire']   = 0;
			if(false == isset($c['path']))     $c['path']     = '';
			if(false == isset($c['domain']))   $c['domain']   = '';
			if(false == isset($c['secure']))   $c['secure']   = false;
			if(false == isset($c['httponly'])) $c['httponly'] = false;

			self::$queue[] = $c;
		}

		public static function clear($name = null)
		{
			if($name === null){
				foreach($_COOKIE as $ckey=>$cval){
					self::set(
						array(
							'name'   => $ckey,
							'value'  => null,
							'expire' => -1
						)
					);	
				}
			}else{
				self::set(
					array(
						'name'   => $name,
						'value'  => null,
						'expire' => -1
					)
				);
			}
		}

		public static function write()
		{
			foreach(self::$queue as $c){
				if(false == setcookie(
					$c['name'],
					$c['value'],
					$c['expire'],
					$c['path'],
					$c['domain'],
					$c['secure'],
					$c['httponly']
				)){
					throw new AppException('Can\'t send cookies');
				}
			}
		}
	}
?>