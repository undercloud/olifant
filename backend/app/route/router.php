<?php
	namespace olifant\route;

	use olifant\http\Request;
	use olifant\http\RequestBuilder;
	use olifant\route\RouteApp;
	use olifant\controller\ControllerClosure;
	use olifant\exceptions\AppException;

	class Router
	{
		private $request = null;

		public function __construct(Request $req)
		{
			$this->request = $req;
		}

		private function cleanPath($path)
		{
			if(false !== ($pos = strpos($path,'/:'))){
				$path = substr($path,0,$pos);
			}

			$path = $this->request->cleanUri($path);

			return $path;
		}

		public static function compare($cleaned, $uri)
		{
			$pattern = '~^' . $cleaned . '(/|$)~u';
			return preg_match($pattern,$uri);
		}

		public function route($route = null)
		{
			$ro = (null !== $route ? (new $route()) : (new RouteApp()));

			$instance = '\\olifant\\route\\RouteBase';
			if(false === is_subclass_of($ro, $instance))
				throw new AppException('Class ' . get_class($ro) . ' is not instanceof ' . $instance);

			$ro->route();

			$ctx = $ro->getContext();
			$map = $ro->getMap();

			$uri  = $this->request->getUri();
			$call = null;

			foreach($map as $mapkey=>$target){
				$cleaned = $this->cleanPath($mapkey);

				if($this->compare($cleaned, $uri) or (null !== $route and $cleaned === '/')){	
					list($call,$options) = $target;

					if($options){
						if(isset($options['method'])){
							$methods = (is_array($options['method']) ? $options['method'] : array($options['method']));
							if(false === in_array(strtolower($_SERVER['REQUEST_METHOD']), $methods)){
								$call = null;
								break;
							}
						}

						if(isset($options['secure'])){
							if(true === $options['secure']){
								if(false == RequestBuilder::isHTTPS()){
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