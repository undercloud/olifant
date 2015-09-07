<?php
	namespace olifant\http;

	use \olifant\http\ResponseBuilder;
	use \olifant\exceptions\AppException;
	use \olifant\http\Utils;

	class Response
	{
		public function prepare()
		{
			return new ResponseBuilder();
		}

		public function send($res)
		{
			if(isset($res->filePath) or isset($res->fileContents)){
				if(isset($res->filePath)){
					$filename = (isset($res->fileName) ? $res->fileName : sprintf('"%s"',addcslashes(basename($res->file), '"\\')));
					$size     = filesize($res->file);
				}else if(isset($res->fileContents)){
					$filename = (isset($res->fileName) ? $res->fileName : 'Untitled');
					$size     = strlen($res->fileContents);
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
					)
				);
			}

			if(false == isset($res->status))
				$res->status = 200;

			if(isset($res->redirect)){
				$res->header['Location'] = $res->redirect;
				if(false == isset($res->status))
					$res->status = 303;
			}

			if(false == isset($res->statusText))
				$res->statusText = Utils::getStatusText($res->status);

			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $res->status . ' ' . $res->statusText);

			foreach($res->header as $key=>$value){
				if(null === $value)
					header_remove($key);
				else
					header($key . ': ' . $value);
			}

			$res->cookies->write();
			
			if(isset($res->filePath)){
				while(@ob_end_flush());
				readfile($res->filePath);
			}else if(isset($res->fileContents)){
				$this->write($res->fileContents);
			}else if(isset($res->body)){
				if(is_scalar($res->body)){
					$this->write($res->body);
				}else if(is_array($res->body) or is_object($res->body)){
					$this->sendJson($res->body);
				}
			}
		}

		public function write($data)
		{
			echo $data;
		}

		public function sendJson($data)
		{
			header('Content-Type: application/json; charset=utf-8');
			
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
	}
?>