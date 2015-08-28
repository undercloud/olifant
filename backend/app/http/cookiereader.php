<?php
	namespace olifant\http;

	class CookieReader
	{
		public static function get($key = false){
			if(false === $key){
				return $_COOKIE;
			}else{
				return (isset($_COOKIE[$key]) ? $_COOKIE[$key]: null);
			}
		}

		public static function has($key){
			return isset($_COOKIE[$key]);
		}
	}
?>