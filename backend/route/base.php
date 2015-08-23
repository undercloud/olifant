<?php
	namespace route;

	abstract class RouteBase 
	{
		private $context = null;
		private $map     = array();

		public function on($path,$call,$option = array())
		{
			$this->map[$path] = array($call,$option);
			return $this;
		}

		public function defaults($call,$option = array())
		{
			$this->on(null,$call,$option);	
			return $this;
		}

		public function assign($context)
		{
			$this->context = $context;
			return $this;
		}

		public function getContext()
		{
			return $this->context;
		}

		public function getMap()
		{
			krsort($this->map);
			return $this->map;
		}

		abstract public function route();
	}
?>