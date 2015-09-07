<?php
	namespace olifant\route;

	use \olifant\http\Request;
	use \olifant\route\RouteApp;
	use \olifant\controller\ControllerClosure;
	use \olifant\exceptions\AppException;

	class Router
	{
		private $req = null;

		public function __construct(Request $req)
		{
			$this->request = $req;
		}

		private function cleanPath($path)
		{
			if(false !== ($pos = strpos($path,':'))){
				$path = substr($path,0,$pos);
				$path = $this->request->cleanUri($path);

				if(!$path)
					$path = '/';
			}

			return $path;
		}

		public function route($route = null)
		{
			$ro = ($route ? (new $route()) : (new RouteApp()));
			$ro->route();

			$ctx = $ro->getContext();
			$map = $ro->getMap();

			$uri    = $this->request->getUri();
			$call   = null;
			$option = array();

			foreach($map as $mapkey=>$target){
				$cleaned = $this->cleanPath($mapkey);
				
				if((0 === stripos($uri,$cleaned)) or (null !== $route and $cleaned[0] == '/')){
					list($call,$option) = $target;

					if($option){
						if(isset($option['method'])){
							$methods = (is_array($option['method']) ? $option['method'] : array($option['method']));
							if(false === in_array(strtolower($_SERVER['REQUEST_METHOD']),$methods)){
								$call = null;
								break;
							}
						}

						if(isset($option['secure'])){
							if(true === $option['secure']){
								if(false == (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on')){
									$call = null;
									break;
								}
							}
						}
					}

					$this->request->setMapKey($mapkey);
					$this->request->excludeSubPath($cleaned);

					break;
				}
			}

			if(null === $call){
				return (object)array(
					'controller' => '\\olifant\\controller\\ControllerError',
					'action'     => 'notFound404'
				);
			}

			if(is_string($call) and false !== strpos($call,'::')){
				list($controller,$action) = explode('::',$call);

				return (object)array(
					'controller' => '\\olifant\\controller\\' . $controller,
					'action'     => $action
				);
			}

			if(is_string($call) and 0 === strpos(strtolower($call),'route')){
				return $this->route('\\olifant\\route\\' . $call);
			}
	
			if(\is_closure($call)){
				return (object)array(
					'controller' => '\\olifant\\controller\\ControllerClosure',
					'action'     => \olifant\controller\ControllerClosure::bind($call)
				);
			}

			if(null !== $ctx){
				return (object)array(
					'controller' => '\\olifant\\controller\\' . $ctx,
					'action'     => $call
				);
			}

			throw new AppException('Unsupported callback type');
		}
	}
?>