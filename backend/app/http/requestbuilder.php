<?php
	namespace olifant\http;

	use \olifant\http\Request;
	use \model\core\http\CookieHelper;
	use \model\core\http\UserStatistics;

	class RequestBuilder
	{
		public function __construct(Request $req)
		{
			$map = array(
				'GET'  => $_GET,
				'POST' => $_POST,
				'CLI'  => $_REQUEST
			);

			$this->ajax        = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
			$this->params      = $req->parseParams();
			$this->originalUrl = $_SERVER['REQUEST_URI'];
			$this->query       = (isset($map[$_SERVER['REQUEST_METHOD']]) ? $map[$_SERVER['REQUEST_METHOD']] : array());
			$this->files       = $_FILES;
			$this->method      = strtolower($_SERVER['REQUEST_METHOD']);
			$this->protocol    = $_SERVER['SERVER_PROTOCOL'];
			$this->port        = $_SERVER['SERVER_PORT'];
			$this->host        = $_SERVER['HTTP_HOST'];
			$this->serverName  = $_SERVER['SERVER_NAME'];
			$this->secure      = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on');
		
			$this->referer = null;
			if(isset($_SERVER['HTTP_REFERER'])){
				$this->referer = $_SERVER['HTTP_REFERER'];
			}

			$this->json = null;
			if(isset($_SERVER['CONTENT_TYPE']) and 0 === strpos($_SERVER['CONTENT_TYPE'],'application/json')){
				$this->json = json_decode(file_get_contents('php://input'),true);
			}
		}

		public function __get($key)
		{
			switch($key){
				case 'cookies':
					return CookieHelper::getReader();

				case 'client':
					return new UserStatistics();

				case 'subdomains':
					$subdomains = array();
					if(false === filter_var($_SERVER['SERVER_NAME'],FILTER_VALIDATE_IP)){
						$subdomains = array_slice(
							array_reverse(
								explode('.',$_SERVER['SERVER_NAME'])
							),2
						);
					}

					return $subdomains;

				case 'header':
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

						return $header;
					}else{
						return getallheaders();
					}
			}
		}
	}
?>