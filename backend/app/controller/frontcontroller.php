<?php
	namespace olifant\controller;

	class FrontController
	{
		protected static $instance = null;
		protected $controller      = null;
		protected $action          = null;
		protected $request         = null;
		protected $response        = null;

		private function __construct(){}
		private function __wakeup(){}

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

		public function setParams($request,$response)
		{
			$this->request  = $request;
			$this->response = $response;

			return $this;
		}

		public function exec()
		{
			if(null === $this->controller)
				throw new \olifant\exceptions\AppException('Class controller is not defined');

			if(false === class_exists($this->controller))
				throw new \olifant\exceptions\AppException('Class ' . $this->controller . ' not found');

			if(false === is_subclass_of($this->controller,'\olifant\controller\ControllerBase'))
				throw new \olifant\exceptions\AppException('Class ' . $this->controller . ' is not instanceof \olifant\controller\ControllerBase');

			if(null === $this->action)
				throw new \olifant\exceptions\AppException('Method ' . $this->action . ' is not defined');

			if('\olifant\controller\ControllerClosure' != $this->controller and false === method_exists($this->controller, $this->action))
				throw new \olifant\exceptions\AppException('Method ' . $this->action .' not found in controller ' . $this->controller);
	
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