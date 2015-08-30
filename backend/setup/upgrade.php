<?php

	if(false == function_exists('array_column')){
		function array_column($array, $column_key, $index_key = null){
			return array_reduce($array,function($result,$item)use($column_key,$index_key){
				if(null === $index_key){
					$result[] = $item[$column_key];
				}else{
					$result[$item[$index_key]] = $item[$column_key];
				}

				return $result;
			},array());
		}
	}

	if(false == function_exists('boolval')){
		function boolval($val){
			return (bool)$val;
		}
	}

	if(false == function_exists('is_closure')){
		function is_closure($t){
			return (is_object($t) and ($t instanceof \Closure));
		}
	}
	
	if(false == function_exists('is_blank')){
		function is_blank($v){
			return (
				($v === '')    or
				($v === null)  or
				($v === false) or
				(is_array($v)  and 0 == count($v)) or
				(is_object($v) and 0 == count((array)$v))
			);
		}
	}
?>