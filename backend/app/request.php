<?php
	namespace app;

	use \model\core\utils\ModelCookieHelper;
	use \model\core\utils\ModelUserStatistics;

	class Request
	{
		private $uri = null;

		public function __construct()
		{
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
			$map = array(
				'GET'  => $_GET,
				'POST' => $_POST,
				'CLI'  => $_REQUEST
			);

			$req               = new \stdClass();
			$req->ajax        = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
			$req->params      = $this->parseParams();
			$req->originalUrl = $_SERVER['REQUEST_URI'];
			$req->query       = (isset($map[$_SERVER['REQUEST_METHOD']]) ? $map[$_SERVER['REQUEST_METHOD']] : array());
			$req->files       = $_FILES;
			$req->method      = strtolower($_SERVER['REQUEST_METHOD']);
			$req->protocol    = $_SERVER['SERVER_PROTOCOL'];
			$req->port        = $_SERVER['SERVER_PORT'];
			$req->host        = $_SERVER['HTTP_HOST'];
			$req->serverName  = $_SERVER['SERVER_NAME'];
			$req->secure      = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on');
			$req->cookies     = ModelCookieHelper::getReader();
			$req->client      = new ModelUserStatistics();

			if(isset($_SERVER['HTTP_REFERER'])){
				$req->referer = $_SERVER['HTTP_REFERER'];
			}

			if(isset($_SERVER['CONTENT_TYPE']) and 0 === strpos($_SERVER['CONTENT_TYPE'],'application/json')){
				$req->json = json_decode(file_get_contents('php://input'),true);
			}

			$req->subdomains = array();
			if(false === filter_var($_SERVER['SERVER_NAME'],FILTER_VALIDATE_IP)){
				$req->subdomains = array_slice(
					array_reverse(
						explode('.',$_SERVER['SERVER_NAME'])
					),2
				);
			}
			
			if(false == function_exists('getallheaders')){
				$header = array();
				$use_header  = array('CONTENT_TYPE','CONTENT_LENGTH');
				foreach($_SERVER as $name => $value){
					if(strtolower(substr($name, 0, 5)) == 'http_'){
						$header[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					}

					if(in_array($name,$use_header)){
						$header[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $name))))] = $value;
					}
				}

				$req->header = $header;
			}else{
				$req->header = getallheaders();
			}

			return $req;
		}
	}
?>