<?php
	namespace app;

	class Registry
	{
		protected static $instance = null;
		
		private $storage      = array();
		private $savekeys     = array();
		private $storage_path = null;
		private $modify       = false;

		private function __construct()
		{
			$this->storage_path = \app\conf\APP_PATH . '/registry/storage.reg';

			if(is_readable($this->storage_path)){
				$this->storage = unserialize(file_get_contents($this->storage_path));
			
				if(false == is_array($this->storage)){
					$this->storage = array();
				}
			}else{
				$this->storage = array();
			}
		}

		public static function getInstance()
		{	
			if(null === self::$instance)
				self::$instance = new self();
			return self::$instance;
		}

		public function contains($key)
		{
			return isset($this->storage[$key]);
		}

		public function get($key)
		{
			if(isset($this->storage[$key]))
				return $this->storage[$key];
			
			return null;
		}

		public function getAll()
		{
			return $this->storage;
		}

		public function set($key,$value,$save = false)
		{
			$this->storage[$key] = $value;

			if(true === $save){
				$this->savekeys[] = $key;
				$this->modify     = true;
			}
		}

		public function remove($key)
		{	
			unset($this->storage[$key]);

			if(isset($this->savekeys[$key])){
				unset($this->savekeys[$key]);
				$this->modify = true;
			}
		}

		public function __destruct()
		{
			if(false === $this->modify)
				return;
			
			if(is_writable($this->storage_path))
				file_put_contents($this->storage_path, serialize(array_intersect_key($this->storage,array_flip($this->savekeys))));
			else
				throw new \app\AppException('Can\'t save registry');
		}
	}
?>