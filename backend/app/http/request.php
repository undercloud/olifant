<?php
	namespace olifant\http;

	use \olifant\http\RequestBuilder;

	class Request
	{
		private $uri = null;

		public function __construct($uri = null)
		{
			if(null === $uri)
				$uri = $_SERVER['REQUEST_URI'];

			if($uri === '/'){
				$this->uri = 'index';
			}else if(0 === strpos($uri,'/?')){
				$this->uri = 'index' . substr($uri,1);
			}else{
				$pos = strpos($uri,'?');
				if($pos !== false)
					$uri = substr($uri,0,$pos);
			
				$this->uri = $uri;
			}

			$this->cleanUri();

			return $this;
		}

		public function getUri()
		{
			return $this->uri;
		}

		private function cleanUri()
		{
			$this->uri = implode(
				'/',
				array_filter(
					explode('/',$this->uri),
					function($v){
						return ($v != '');
					}	
				)
			);
		}

		public function excludeSubPath($subpath)
		{
			$this->uri = preg_replace('/^' . addcslashes($subpath,'/') . '/i','',$this->uri,1);
			$this->cleanUri();
		}

		public function parseParams()
		{
			return $this->uri ? explode('/',$this->uri) : array();
		}

		public function build()
		{
			return new RequestBuilder($this);
		}
	}
?>