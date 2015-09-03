<?php
	namespace olifant\http;

	use \olifant\http\RequestBuilder;

	class Request
	{
		private $uri    = null;
		private $mapkey = null;

		public function __construct($uri = null)
		{
			if(null === $uri)
				$uri = $_SERVER['REQUEST_URI'];

			if(!$uri){
				$uri = '/';
			}else{
				$pos = strpos($uri,'?');
				if($pos !== false)
					$uri = substr($uri,0,$pos);

				$this->uri = $this->cleanUri($uri);
			}
		}

		public function getUri()
		{
			return $this->uri;
		}

		public function cleanUri($uri)
		{
			$uri = trim($uri,' /\\');

			if(!$uri)
				$uri = '/';

			return $uri;
		}

		public function excludeSubPath($mapkey)
		{
			if($mapkey != '/'){
				$rx = "/^" . addcslashes($mapkey,'/') . "/i";

				$this->mapkey = preg_replace($rx,'',$this->mapkey,1);
				$this->uri    = preg_replace($rx,'',$this->uri,1);
				
				$this->uri = $this->cleanUri($this->uri);
			}

			return $this;
		}

		public function setMapKey($mapkey)
		{
			$this->mapkey = $mapkey;

			return $this;
		}

		public function parseParams()
		{
			$params = array();
			if($this->uri)
				$params = array_values(
					array_filter(
						explode('/',$this->uri),
						function($v){
							return (false == is_blank($v));
						}
					)
				);

			if($this->mapkey){
				$segments = array_values(
					array_filter(
						explode('/:',$this->mapkey),
						function($v){
							return (false == is_blank($v));
						}
					)
				);

				foreach($segments as $k=>$p){
					if(isset($params[$k])){
						$params[$p] = $params[$k];
						unset($params[$k]);
					}else{
						$params[$p] = null;
					}
				}
			}

			return $params;
		}

		public function build()
		{
			return new RequestBuilder($this);
		}
	}
?>