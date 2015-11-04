<?php
	namespace olifant\http;

	use olifant\http\ResponseBuilder;
	use olifant\exceptions\AppException;
	use olifant\http\JSON;
	use olifant\http\Utils;

	class Response
	{
		public function send(ResponseBuilder &$res)
		{
			if('CLI' != $_SERVER['REQUEST_METHOD']){
				if(isset($res->file)){
					if(isset($res->file->path)){
						if(false == is_readable($res->file->path)){
							throw new AppException('Can\' access file ' . $res->file->path);
						}

						if(false == is_file($res->file->path)){
							throw new AppException('File ' . $res->file->path . ' not found');
						}

						$filename = (isset($res->file->name) ? $res->file->name : sprintf('"%s"',addcslashes(basename($res->file->path), '"\\')));
						$size     = filesize($res->file->path);
					}else if(isset($res->file->contents)){
						$filename = (isset($res->file->name) ? $res->file->name : 'Untitled');
						$size     = strlen($res->file->contents);
					}

					if(false == isset($res->file->header) or false == is_array($res->file->header)){
						$res->file->header = array();
					}

					$res->header = array_merge(
						$res->header,
						array(
							'Content-Description' => 'File Transfer',
							'Content-Type' => 'application/octet-stream',
							'Content-Disposition' => 'attachment; filename=' . $filename,
							'Content-Transfer-Encoding' => 'binary',
							'Connection' => 'Keep-Alive',
							'Expires' => '0',
							'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
							'Pragma' => 'public',
							'Content-Length' => $size
						),
						$res->file->header
					);
				}

				if(false == isset($res->status))
					$res->status = 200;

				if(isset($res->redirect)){
					$res->header['Location'] = $res->redirect;
					if(false == isset($res->status))
						$res->status = 303;
				}

				if(isset($res->refresh)){
					if(false === isset($res->refresh->timeout))
						$res->refresh->timeout = 0;

					$res->header['Refresh'] = $res->refresh->timeout . ' ;url=' . rawurlencode($res->refresh->url); 
				}

				if(false == isset($res->statusText))
					$res->statusText = Utils::getStatusText($res->status);

				if(is_array($res->body) or is_object($res->body)){
					$res->header['Content-Type'] = 'application/json; charset=utf-8';
				}

				header($_SERVER['SERVER_PROTOCOL'] . ' ' . $res->status . ' ' . $res->statusText);

				foreach($res->header as $key=>$value){
					if(null === $value)
						header_remove($key);
					else
						header($key . ': ' . $value);
				}

				$res->cookies->write();
			}
			
			if(isset($res->file)){
				if(isset($res->file->path)){
					while(@ob_end_flush());
					readfile($res->file->path);
				}else if(isset($res->file->contents)){
					$this->write($res->file->contents);
				}
			}else if(isset($res->body)){
				if(is_scalar($res->body)){
					$this->write($res->body);
				}else if(is_array($res->body) or is_object($res->body)){
					$this->write(
						JSON::encode($res->body)
					);
				}
			}
		}
		
		public function write($data)
		{
			echo $data;
		}

		public static function end(ResponseBuilder &$res)
		{
			$helper = new self();
			$helper->send($res);
			
			exit(0);
		}
	}
?>