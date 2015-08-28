<?php
	namespace olifant\http;

	use \olifant\http\CookieReader;
	use \olifant\http\CookieWriter;

	class CookieHelper
	{
		public static function getReader()
		{
			return new CookieReader();
		}

		public static function getWriter()
		{
			return new CookieWriter();
		}
	}	
?>