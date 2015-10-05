<?php
	namespace olifant;

	use \olifant\exceptions\AppException;

	class Settings
	{
		private static $cursor = null;
		private static $map = array();

		public static function getInstance()
		{
			return new self(); 
		}

		public function addSection($section)
		{
			self::$map[$section] = array();
			self::$cursor = $section;

			return $this;
		}

		public function set($name,$value)
		{
			self::$map[self::$cursor][$name] = $value;

			return $this;
		}


		public static function get($path = null/*,$default = null */)
		{
			$array = self::$map;
			if(!empty($path)){
				$keys = preg_split('/[:\.]/',$path);

				foreach($keys as $key){
					if(isset($array[$key])){
						$array = $array[$key];
					}else{
						$args = func_get_args();
						if(1 == count($args))
							throw new AppException();

						return $args[1];
					}
				}
			}

			return $array;
		}
	}
?>