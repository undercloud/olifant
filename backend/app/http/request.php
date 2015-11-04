<?php
	namespace olifant\http;

	use olifant\http\RequestBuilder;

	class Request
	{
		private $uri      = null;
		private $mapkey   = null;
		private $chain    = '';

		public function __construct($uri)
		{
			$uri = rawurldecode($uri);
			$this->uri = $this->cleanUri($uri);
		}

		public function getUri()
		{
			return $this->uri;
		}

		public static function cleanUri($uri)
		{
			$uri = rtrim($uri,' /\\');

			if(!$uri){
				$uri = '/';
			}

			return $uri;
		}

		public function excludeSubPath($mapkey)
		{
			if($mapkey != '/'){
				$rx = '~^' . $mapkey . '~u';

				$chain = &$this->chain;

				$replace = function($m)use(&$chain){
					$chain .= $m[0];

					return '';
				};

				$this->mapkey = (string)substr($this->mapkey, strlen($mapkey));
				$this->uri    = preg_replace_callback($rx, $replace, $this->uri, 1);
			}

			return $this;
		}

		public function setMapKey($mapkey)
		{
			$this->mapkey = $mapkey;
			return $this;
		}

		public function getMapStack()
		{
			return $this->cleanUri($this->chain);
		}

		public function parseParams()
		{
			$params = array();
			if($this->uri){
				$params = array_values(
					array_filter(
						explode('/', $this->uri),
						function($v){
							return (false == is_blank($v));
						}
					)
				);
			}

			array_walk($params,function(&$v){
				$v = rawurldecode($v);
			});

			if($this->mapkey){
				$segments = array_values(
					array_filter(
						explode('/:', $this->mapkey),
						function($v){
							return (false == is_blank($v) and ($v != '/'));
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
	}
?>