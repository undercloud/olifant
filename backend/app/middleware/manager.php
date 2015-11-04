<?php
	namespace olifant\middleware;

	use olifant\route\Router;
	use olifant\http\Request;
	use olifant\http\RequestBuilder;
	use olifant\http\ResponseBuilder;
	use olifant\exceptions\AppException;

	class MiddlewareManager
	{
		protected static $instance = null;
		protected static $before   = array();
		protected static $after    = array();

		private function __construct(){}
		private function __wakeup(){}
		private function __clone(){}

		public static function getInstance()
		{
			if(null === self::$instance){
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function registerBefore(array $before)
		{
			self::$before = array_merge(self::$before, $before);

			return $this;
		}

		public function registerAfter(array $after)
		{
			self::$after = array_merge(self::$after, $after);
			
			return $this;
		}

		private function resolve(array $source, RequestBuilder &$req, ResponseBuilder &$res, \stdClass &$call)
		{
			if($source){
				foreach($source as $mwobject){
					$class = '\\olifant\\middleware\\' . $mwobject;
					$middle = new $class;

					$instance = '\\olifant\\middleware\\MiddlewareBase';
					if(false === is_subclass_of($middle, $instance)){
						throw new AppException('Class ' . $class . ' is not instanceof ' . $instance);
					}

					if(isset($middle->path) or isset($middle->exceptPath)){
						$uri = rawurldecode(Request::cleanUri($req->path));
						$mode = (
							isset($middle->path)
							? false
							: (
								isset($middle->exceptPath)
								? true
								: false
							)
						);

						$target = (
							isset($middle->path)
							? $middle->path
							: (
								isset($middle->exceptPath)
								? $middle->exceptPath
								: null
							)
						);

						if(is_array($target)){
							$match = false;
							foreach($target as $p){
								if(Router::compare($p, $uri)){
									$match = true;
									break;
								}
							}

							if($mode === $match){
								continue;
							}
						}else{
							if($mode === (Router::compare($target, $uri))){
								continue;
							}
						}
					}

					call_user_func_array(
						array(
							$middle,
							'handle'
						),
						array(
							$req,
							$res,
							$call
						)
					);
				}
			}

			return $this;
		}

		public function before(&$req, &$res, &$call)
		{
			return $this->resolve(self::$before, $req, $res, $call);
		}

		public function after(&$req,&$res,&$call)
		{
			return $this->resolve(self::$after, $req, $res, $call);
		}
	}
?>