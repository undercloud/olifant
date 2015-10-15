<?php
	namespace olifant\http;

	use \olifant\http\Request;
	use \olifant\http\CookieHelper;
	use \olifant\http\UserStatistics;
	use \olifant\http\Auth;

	class RequestBuilder
	{
		public function __construct(Request $req)
		{
			$map = array(
				'GET'  => $_GET,
				'POST' => $_POST,
				'CLI'  => $_REQUEST
			);

			$this->mapkey  = $req->getMapStack();
			$this->ajax    = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
			$this->params  = $req->parseParams();
			$this->url     = $_SERVER['REQUEST_URI'];
			$this->path    = $_SERVER['QUERY_PATH'];
			$this->query   = (isset($map[$_SERVER['REQUEST_METHOD']]) ? $map[$_SERVER['REQUEST_METHOD']] : array());
			$this->method  = strtolower($_SERVER['REQUEST_METHOD']);
			
			$this->overflow = (
				$_SERVER['REQUEST_METHOD'] == 'POST' && 
				empty($_POST) &&
     			empty($_FILES) && 
     			$_SERVER['CONTENT_LENGTH'] > 0
			);

			if(isset($_SERVER['protocol']))
				$this->protocol = $_SERVER['SERVER_PROTOCOL'];
			
			if(isset($_SERVER['port']))
				$this->port = (int)$_SERVER['SERVER_PORT'];

			if(isset($_SERVER['SERVER_NAME']))
				$this->host = $_SERVER['SERVER_NAME'];

			$this->secure = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on');
		
			if(isset($_SERVER['HTTP_REFERER'])){
				$this->referer = $_SERVER['HTTP_REFERER'];
			}

			if(isset($_SERVER['CONTENT_TYPE']) and 0 === strpos($_SERVER['CONTENT_TYPE'],'application/json')){
				$this->json = json_decode(file_get_contents('php://input'),true);
			}
		}

		public function __get($key)
		{
			switch($key){
				case 'files':
					$reorder = array();

					foreach($_FILES as $key => $all){
						if(is_array($all['name'])){
							foreach($all as $i => $val){
								$reorder[$i][$key] = $val;    
							}
						}else{
							$reorder[$key] = $all;
						}
					}

					return ($this->files = $reorder);

				case 'cookies':
					return ($this->cookies = CookieHelper::getReader());

				case 'client':
					return ($this->client = new UserStatistics());

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
							if(strtolower(substr($name, 0, 5)) == 'http_'){
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

				case 'auth':
					return ($this->auth = new Auth());
			}
		}
	}
?>