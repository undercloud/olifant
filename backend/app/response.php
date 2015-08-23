<?php
	namespace app;

	use \model\core\utils\ModelCookieHelper;
	use \app\AppException;

	class Response
	{
		public function prepare()
		{
			$res = new \stdClass();

			$res->status     = 200;
			$res->statusText = 'OK';
			$res->header     = array();
			$res->cookies    = ModelCookieHelper::getWriter();
			$res->body       = null;

			return $res;
		}

		public function send($res)
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $res->status . ' ' . $res->statusText);

			foreach($res->header as $key=>$value){
				if(null === $value)
					header_remove($key);
				else
					header($key . ': ' . $value);
			}

			$res->cookies->write();

			if(isset($res->body)){
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