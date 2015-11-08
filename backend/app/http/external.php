<?php
	namespace olifant\http;

	use olifant\exceptions\AppException;
	use olifant\http\JSON;

	class External
	{
		private $url         = '';
		private $context     = array();
		private $stream      = null;
		private $protocol    = '';
		private $status      = 0;
		private $statusText  = '';
		private $headers     = array();
		private $body        = null;

		public function __construct($url,$options = array())
		{
			$this->url = $url;

			if(false === isset($options['protocol'])){
				$options['protocol'] = 'http';
			}

			if(false == isset($options['method'])){
				$options['method'] = 'GET';
			}else{
				$options['method'] = strtoupper($options['method']);
			}

			if(false == isset($options['content'])){
				$options['content'] = array();
			}

			if(false == isset($options['content-type'])){
				$options['content-type'] = 'urlencode';
			}

			$boundary = "---------------------" . substr(md5(rand(0,32000)), 0, 10);

			switch($options['content-type']){
				case 'urlencode':
					if($options['method'] == 'GET' or $options['method'] == 'DELETE'){
						if($options['content']){
							$this->url .= '?' . rawurldecode(http_build_query($options['content']));
						}

						$options['content'] = '';
					}else{
						$options['content'] = http_build_query($options['content']);
					}

					$options['header']['Content-Type'] = 'application/x-www-form-urlencoded';
				break;

				case 'json':
					$options['content'] = JSON::encode($options['content']);
					$options['header']['Content-Type'] = 'application/json';
				break;

				case 'form':
					if($options['content']){
						$vars = explode('&',http_build_query($options['content']));
						
						$options['content'] = "--{$boundary}\n";

						if($vars){
							foreach($vars as $v){
								list($key,$value) = explode('=',$v,2);

								$key = rawurldecode($key);

								$options['content'] .= "Content-Disposition: form-data; name=\"{$key}\"\n\n{$value}\n";
								$options['content'] .= "--{$boundary}\n";
							}
						}
					}else{
						$options['content'] = "--{$boundary}\n";
					}

					$options['header']['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
				break;
			}
			

			if(isset($options['files'])){
				foreach($options['files'] as $file){
					if(false == is_readable($file['path'])){
						throw new AppException('Can\' access file ' . $file['path']);
					}

					$options['content'] .= "Content-Disposition: form-data; name=\"{$file['name']}\"; filename=\"" . basename($file['path']) . "\"\n";
					
					if(isset($file['type'])){
						$options['content'] .= "Content-Type: {$file['type']}\n";
					}

					$options['content'] .= "Content-Transfer-Encoding: binary\n\n";
					$options['content'] .= file_get_contents($file['path'])."\n";
					$options['content'] .= "--{$boundary}\n";
				}
			}

			$options['Content-Length'] = strlen($options['content']);

			if(isset($options['header'])){
				$options['header'] = implode('',array_map(
					function($k,$v){
						return $k . ': ' . $v . "\r\n";
					},
					array_keys($options['header']),
					array_values($options['header'])
				));
			}

			if(false === isset($options['timeout'])){
				$options['timeout'] = 25;
			}

			if(false === isset($options['ignore_errors'])){
				$options['ignore_errors'] = true;
			}

			$protocol = $options['protocol'];
			unset($options['protocol']);

			unset($options['content-type']);

			$this->context = stream_context_create(
				array(
					$protocol => $options
				)
			);
		}

		public function send()
		{
			$this->stream = @fopen($this->url, 'r', false, $this->context);

			foreach($http_response_header as $index=>$head){
				if($index === 0){
					list($this->protocol,$this->status,$this->statusText) = explode(' ',$head); 
				}else{
					list($key,$value) = explode(':',$head,2);
					$this->headers[trim($key)] = trim($value);
				}
			}

			return $this;
		}

		public function getProtocol()
		{
			return $this->protocol;
		}

		public function getStatus()
		{
			return $this->status;
		}

		public function getStatusText()
		{
			return $this->statusText;
		}

		public function getHeaders()
		{
			return $this->headers;
		}

		public function getBody()
		{
			if(null !== $this->body)
				return $this->body;

			$this->body = '';
			
			if(false === $this->stream){
				return $this->body;
			}

			while(false === feof($this->stream)){
				$this->body .= fread($this->stream, 1024);
			}

			return $this->body;
		}

		public function saveToFile($path)
		{
			$dest = @fopen($path,'w');

			if(false === $dest){
				throw new AppException('Can\' access file ' . $path);
			}

			flock($dest, LOCK_EX);
			$bytes = stream_copy_to_stream($this->stream,$dest);
			flock($dest, LOCK_UN);
			fclose($dest);

			return $bytes;
		}

		public function __destruct()
		{
			if(false !== $this->stream){
				fclose($this->stream);
			}
		}
	}
?>