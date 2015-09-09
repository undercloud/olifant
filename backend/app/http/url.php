<?php
	namespace olifant\http;

	class Url
	{
		private static $default_scheme = 'unsupportedschemetype';
		private static $parts = array('scheme','user','pass','host','port','path','query','fragment');

		private function normalize($url)
		{
			if(0 === stripos($url,'//')){
				$url = self::$default_scheme . ':' . $url;
			}else if(false === stripos($url,'://')){
				$url = self::$default_scheme . '://' . $url;
			}

			return $url;
		}

		public function __construct($url = null)
		{
			if(null === $url){
				foreach(self::$parts as $p){
					$this->$p = null; 
				}
			}else{
				$url = $this->normalize($url);
				
				$parsed = @parse_url($url);

				foreach(self::$parts as $p){
					$this->$p = ((isset($parsed[$p])) ? $parsed[$p] : null);
				}

				if($this->query){
					parse_str($this->query,$this->query);
				}else{
					$this->query = array();
				}

				if($this->scheme === self::$default_scheme)
					$this->scheme = null;
			}
		}

		private function join(array $parts)
		{
			$inline = '';
			if(isset($parts['scheme']))   $inline .= $parts['scheme'] . '://';

			if(isset($parts['user'])){
				$inline .= $parts['user'];
				if(isset($parts['pass'])){
					$inline .= ':' . $parts['pass'];
				}

				$inline .= '@';
			}

			if(isset($parts['host']))     $inline .= $parts['host'];
			if(isset($parts['port']))     $inline .= ':' . $parts['port'];
			if(isset($parts['path']))     $inline .= $parts['path'];
			if(isset($parts['query']))    $inline .= '?' . $parts['query'];
			if(isset($parts['fragment'])) $inline .= '#' . $parts['fragment'];

			return $inline;
		}

		public function build()
		{
			$prepare = array();
			foreach(self::$parts as $p){
				if($this->$p){
					if('query' === $p){
						$prepare[$p] = http_build_query($this->$p);
					}else{
						$prepare[$p] = $this->$p;
					}
				}
			}

			return $this->join($prepare);
		}
	}
?>