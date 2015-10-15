<?php
	namespace olifant\controller;

	use \olifant\Settings;
	use \olifant\http\RequestBuilder;
	use \olifant\http\ResponseBuilder;
	use \olifant\exceptions\AppException;

	class FrontController
	{
		protected static $instance = null;
		protected $controller      = null;
		protected $action          = null;
		protected $request         = null;
		protected $response        = null;

		private function __construct(){}
		private function __wakeup(){}
		private function __clone(){}

		public static function getInstance()
		{
			if(null === self::$instance)
				self::$instance = new self();

			return self::$instance;
		}

		public function setController($controller)
		{
			$this->controller = $controller;
			return $this;
		}

		public function setAction($action)
		{
			$this->action = $action;
			return $this;
		}

		public function setParams(RequestBuilder $request, ResponseBuilder $response)
		{
			$this->request  = $request;
			$this->response = $response;

			return $this;
		}

		public function exec()
		{
			//if('debug' == Settings::get('system.devmode',null)){
				if(null === $this->controller)
					throw new \olifant\exceptions\AppException('Class controller is not defined');

				if(false === class_exists($this->controller))
					throw new AppException('Class ' . $this->controller . ' not found');

				$instance = '\olifant\controller\ControllerBase';
				if(false === is_subclass_of($this->controller,$instance))
					throw new AppException('Class ' . $this->controller . ' is not instanceof ' . $instance);

				if(null === $this->action)
					throw new AppException('Call method is not defined');

				if('\olifant\controller\ControllerClosure' != $this->controller and false === method_exists($this->controller, $this->action))
					throw new AppException('Method ' . $this->action .' not found in controller ' . $this->controller);
			//}

			return call_user_func_array(
				array(
					new $this->controller(), 
					$this->action
				), 
				array(
					$this->request,
					$this->response
				)
			);
		}
	}
?>