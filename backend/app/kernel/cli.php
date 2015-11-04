<?php
	namespace olifant;

	class CLI
	{
		private static $color = array(
			'black'  => '0;30',
			'blue'   => '0;34',
			'green'  => '0;32',
			'cyan'   => '0;36',
			'red'    => '0;31',
			'purple' => '0;35',
			'brown'  => '0;33',
			'yellow' => '1;33',
			'gray'   => '0;37',
			'white'  => '1;37'
		);

		private static $bg = array(
			'black'      => '40',
			'red'        => '41',
			'green'      => '42',
			'yellow'     => '43',
			'blue'       => '44',
			'magenta'    => '45',
			'cyan'       => '46',
			'light-gray' => '47'
		);

		public function read($prompt = null)
		{
			echo $prompt . ' ';
			return rtrim(
				fgets(
					fopen("php://stdin","r")
				),
				PHP_EOL
			);
		}

		public function param($index = null)
		{
			if(null !== $index){
				return ((isset($_REQUEST[$index])) ? $_REQUEST[$index] : null);
			}

			return $_REQUEST;
		}

		public function process($proc, $arguments = array())
		{
			if($arguments){
				array_walk($arguments, function(&$v){
					$v = escapeshellarg($v);
				});

				$proc = $proc . ' ' . implode(' ', $arguments);
			}

			$output = array();
			$status = 0;

			exec($proc, $output, $status);

			return array(
				'status' => $status,
				'output' => implode(PHP_EOL, $output)
			);
		}

		public function highlight($msg = null, $color = null, $bg = null)
		{
			$before = '';
			if(null === $color or array_key_exists($color, self::$color)){
				$before .= "\033[" . self::$color[$color] . "m";
			}

			if(null === $color or array_key_exists($bg, self::$bg)){
				$before .= "\033[" . self::$bg[$bg] . "m";
			}

			return $before . $msg . "\033[0m";
		}

		public function write($out = null)
		{
			if(is_scalar($out)){
				echo $out . PHP_EOL;
			}else{
				print_r($out) . PHP_EOL;
			}
		}
	}
?>