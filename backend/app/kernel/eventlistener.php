<?php
	namespace olifant\kernel;

	class EventListener
	{
		private $stack = array();
		private static $instance = null;

		private function __construct(){}
		private function __wakeup(){}

		public static function getInstance()
		{	
			if(null === self::$instance)
				self::$instance = new self();
			return self::$instance;
		}

		public function on($e,$call)
		{
			$this->stack[$e][] = $call;
			return $this;
		}

		public function binded($e,$call)
		{
			if(isset($this->stack[$e]))
				foreach($this->stack[$e] as $i=>$c)
					if($c === $call)
						return true;

			return false;
		}

		public function off($e,$call = false)
		{
			if(false === $call)
				unset($this->stack[$e]);
			else if(isset($this->stack[$e]))
				foreach($this->stack[$e] as $i=>$c)
					if($c === $call)
						unset($this->stack[$e][$i]);

			return $this;
		}

		public function trigger($e,array $args = array())
		{
			if(isset($this->stack[$e]))
				foreach($this->stack[$e] as $c)
					if(is_callable($c))
						call_user_func_array($c,$args);

			return $this;
		}
	}
?>