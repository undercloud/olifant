<?php
	namespace olifant\http;

	use olifant\http\Request;
	use olifant\http\CookieReader;
	use olifant\http\Client;
	use olifant\http\Auth;
	use olifant\exceptions\AppException;

	class RequestBuilder
	{
		public function __construct(Request $req)
		{
			$this->mapkey  = $req->getMapStack();
			$this->ajax    = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
			$this->params  = $req->parseParams();
			$this->url     = $_SERVER['REQUEST_URI'];
			$this->path    = $_SERVER['QUERY_PATH'];
			$this->method  = strtolower($_SERVER['REQUEST_METHOD']);

			if(isset($_SERVER['SERVER_PROTOCOL']))
				$this->protocol = $_SERVER['SERVER_PROTOCOL'];
			
			if(isset($_SERVER['SERVER_PORT']))
				$this->port = (int)$_SERVER['SERVER_PORT'];

			if(isset($_SERVER['SERVER_NAME']))
				$this->host = $_SERVER['SERVER_NAME'];

			$this->secure = self::isHTTPS();
		
			if(isset($_SERVER['HTTP_REFERER'])){
				$this->referer = $_SERVER['HTTP_REFERER'];
			}
		}

		public static function isHTTPS()
		{
			if(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'){
				return true;
			}else if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'){
				return true;
			}else if(!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'){
				return true;
			}

			return false;
		}

		public function __get($key)
		{
			switch($key){
				case 'overflow':
					$this->overflow = false;
					
					if($this->method == 'put'){
						if((int)ini_get('post_max_size') > (int)$_SERVER['CONTENT_LENGTH']){
							$this->overflow = true;
						}
					}else if($this->method == 'post'){
						$this->overflow = (
							empty($_POST) and
							empty($_FILES) and 
							$_SERVER['CONTENT_LENGTH'] > 0
						);
					}

					return $this->overflow;

				case 'query':
					if(in_array($this->method,array('put'))){
						if(false == $this->overflow){
							parse_str(
								file_get_contents('php://input'),
								$_REQUEST
							);
						}
					}

					$map = array(
						'get'    => $_GET,
						'post'   => $_POST,
						'put'    => $_REQUEST,
						'delete' => $_REQUEST,
						'cli'    => $_REQUEST
					);
					
					return ($this->query = (isset($map[$this->method]) ? $map[$this->method] : array()));
				
				case 'files':
					$reorder = array();

					foreach($_FILES as $key => $all){
						if(is_array($all['name'])){
							foreach($all as $property=>$items){
								foreach($items as $index=>$value){
									$reorder[$key][$index][$property] = $value;
								}    
							}
						}else{
							$reorder[$key] = array($all);
						}
					}

					return ($this->files = $reorder);

				case 'cookies':
					return ($this->cookies = new CookieReader());

				case 'client':
					return ($this->client = new Client());

				case 'subdomains':
					$this->subdomains = array();
					if(false === filter_var($_SERVER['SERVER_NAME'],FILTER_VALIDATE_IP)){
						$this->subdomains = array_slice(
							array_reverse(
								explode('.',$_SERVER['SERVER_NAME'])
							),2
						);
					}

					return $this->subdomains;

				case 'header':
					if(false == function_exists('getallheaders')){
						$header = array();
						$use_header  = array('CONTENT_TYPE','CONTENT_LENGTH');
						foreach($_SERVER as $name => $value){
							if(strtolower(substr($name, 0, 5)) === 'http_'){
								$header[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
							}

							if(in_array($name,$use_header)){
								$header[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $name))))] = $value;
							}
						}

						return ($this->header = $header);
					}else{
						return ($this->header = getallheaders());
					}

				case 'json':
					if(isset($_SERVER['CONTENT_TYPE']) and 0 === strpos($_SERVER['CONTENT_TYPE'],'application/json')){
						$this->json = json_decode(file_get_contents('php://input'),true);
						$this->query = array();
					}else{
						$this->json = array();
					}

					return $this->json;

				case 'auth':
					return ($this->auth = new Auth());

				default:
					throw new AppException('Undefined property: ' . __CLASS__ . '::' . $key);
			}
		}
	}
?>