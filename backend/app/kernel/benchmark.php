<?php
	namespace olifant;

	class Benchmark
	{
		private $stack  = array();
		private $time   = 0;
		private $memory = 0;

		public function __construct()
		{
			$this->time   = microtime(true);
			$this->memory = memory_get_usage(true); 

			$this->stack['__init__'] = array(
				'time'   => microtime(true),
				'memory' => memory_get_usage(true)
			);
		}

		private function formatMemory($memory)
		{
			return round($memory / 1024 / 1024,2);
		}

		private function formatTime($time)
		{
			return round($time,2);
		}

		public function mark($name)
		{
			$last = end($this->stack);

			$state = array(
				'time'   => microtime(true),
				'memory' => memory_get_usage(true)
			);

			$this->stack[$name] = $state;

			return (object)array(
				'time'   => $this->formatTime($state['time'] - $last['time']),
				'memory' => $this->formatMemory($state['memory'] - $last['memory'])
			);
		}

		public function diff($from,$to)
		{
			
		}

		public function stack()
		{
			var_dump($this->stack);
		}

		public function __get($key)
		{
			switch($key){
				case 'time':
					return $this->formatTime(microtime(true) - $this->time); 
				break;

				case 'memory':
					return $this->formatMemory(memory_get_usage(true) - $this->memory);
				break;
			}
		}
	}
?>