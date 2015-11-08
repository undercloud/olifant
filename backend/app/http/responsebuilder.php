<?php
	namespace olifant\http;

	use olifant\http\CookieWriter;
	use olifant\exceptions\AppException;

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
					return ($this->cookies = new CookieWriter());

				case 'file':
				case 'refresh':
					return ($this->{$key} = new \stdClass);

				default:
					throw new AppException('Undefined property: ' . __CLASS__ . '::' . $key);
			}
		}
	}
?>