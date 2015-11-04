<?php
	namespace olifant\fn;

	function is_php($version)
	{
		$version = (string)$version;
		return version_compare(PHP_VERSION,$version,'>=');
	}

	function random($limit = 8,array $alph = array())
	{
		if(!$alph){
			$alph = array_merge(
				range('A','Z'),
				range('a','z'),
				range('0','9')
			);
		}
	
		shuffle($alph);
		$alph_len = count($alph);
	
		$s = '';
		for($i=0;$i<$limit;$i++){
			$s .= $alph[floor((mt_rand() / mt_getrandmax()) * $alph_len)];
		}
		
		return $s;
	}

	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
	
	function with($object)
	{
		return $object;
	}
?>