<?php
	namespace olifant\http;

	use olifant\exceptions\AppException;

	class CookieWriter
	{
		private static $queue = array();

		public static function set(array $c, $raw = false)
		{
			if(false == isset($c['name'])){
				throw new AppException('Invalid cookie params');
			}

			$c['raw'] = $raw;

			if(false == isset($c['value']))    $c['value']    = '';
			if(false == isset($c['expire']))   $c['expire']   = 0;
			if(false == isset($c['path']))     $c['path']     = '';
			if(false == isset($c['domain']))   $c['domain']   = '';
			if(false == isset($c['secure']))   $c['secure']   = false;
			if(false == isset($c['httponly'])) $c['httponly'] = false;

			$_COOKIE[$c['name']] = $c['value'];
			self::$queue[]       = $c;
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

					unset($_COOKIE[$ckey]);
				}
			}else{
				self::set(
					array(
						'name'   => $name,
						'value'  => null,
						'expire' => -1
					)
				);

				if(isset($_COOKIE[$name]))
					unset($_COOKIE[$name]);
			}
		}

		public static function write()
		{
			foreach(self::$queue as $c){

				$arguments = array(
					$c['name'],
					$c['value'],
					$c['expire'],
					$c['path'],
					$c['domain'],
					$c['secure'],
					$c['httponly']
				);

				$fn = (($c['raw'] === true) ? 'setrawcookie(name)' : 'setcookie');

				if(false == call_user_func_array($fn, $arguments)){
					throw new AppException('Can\'t send cookies for \'' . $c['name'] . '\'');
				}
			}
		}
	}
?>