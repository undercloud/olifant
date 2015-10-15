<?php
	namespace olifant\http;

	use \olifant\http\CookieHelper;

	class ResponseBuilder
	{
		public function __construct()
		{
			$this->header = array();
			$this->body   = null;
		}

		public function __get($key)
		{
			switch($key){
				case 'cookies':
					return ($this->cookies = CookieHelper::getWriter());

				case 'file':
					return ($this->file = new \stdClass);
			}
		}
	}
?>