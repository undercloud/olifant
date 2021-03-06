<?php
	namespace olifant\route;

	use olifant\exceptions\AppException;

	abstract class RouteBase 
	{
		private $context = null;
		private $map     = array();

		public function on($path, $call, $option = array())
		{
			if(array_key_exists($path, $this->map)){
				throw new AppException('Route ' . $path . ' already exists');
			}

			$this->map[$path] = array($call, $option);
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