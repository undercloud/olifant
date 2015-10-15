<?php
	namespace olifant\http;

	use \olifant\http\ResponseBuilder;
	use \olifant\exceptions\AppException;
	use \olifant\http\Utils;

	class Response
	{
		public function send(ResponseBuilder &$res)
		{
			if('CLI' != $_SERVER['REQUEST_METHOD']){
				if(isset($res->file)){
					if(isset($res->file->path)){
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

				if(isset($res->refreshUrl)){
					if(false === isset($res->refreshTimeout))
						$res->refreshTimeout = 0;

					$res->header['Refresh'] = $res->refreshTimeout . ' ;url=' . rawurlencode($res->refreshUrl); 
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
				if(isset($res->file->Path)){
					while(@ob_end_flush());
					readfile($res->file->Path);
				}else if(isset($res->file->contents)){
					$this->write($res->file->contents);
				}
			}else if(isset($res->body)){
				if(is_scalar($res->body)){
					$this->write($res->body);
				}else if(is_array($res->body) or is_object($res->body)){
					$this->sendJson($res->body);
				}
			}
		}

		public function sendJson($data)
		{
			$encoded = json_encode(
				$data,
				JSON_HEX_TAG  | 
				JSON_HEX_AMP  | 
				JSON_HEX_APOS | 
				JSON_HEX_QUOT | 
				JSON_FORCE_OBJECT
			);

			$last_error = json_last_error();

			if($last_error == JSON_ERROR_NONE)
				return $this->write($encoded);
			else
				throw new AppException(
					'Malformed JSON ' . 
					$last_error .
					(
						function_exists('json_last_error_msg')
						? (' ' . json_last_error_msg())
						: ''
					)
				);
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