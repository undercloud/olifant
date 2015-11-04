<?php	
	namespace olifant\http;

	use olifant\http\ResponseBuilder;

	class Auth
	{
		public function __construct()
		{
			$this->login = (isset($_SERVER['PHP_AUTH_USER']) or isset($_SERVER['PHP_AUTH_DIGEST']));
			$this->type  = 'Basic';
			$this->realm = '';
			$this->user  = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
			$this->pass  = (isset($_SERVER['PHP_AUTH_PW'])   ? $_SERVER['PHP_AUTH_PW']   : null);
		}

		public function ask(ResponseBuilder &$res)
		{
			if(false == in_array($this->type, array('Basic','Digest')))
				throw new AppException('Unknown auth type: ' . $this->type);

			$opt = array(
				'realm="' . $this->realm . '"'
			);

			if('Digest' === $this->type){
				$opt = array_merge(
					$opt,
					array(
						'qop="auth"',
						'nonce="' . uniqid() . '"',
						'opaque="' . md5($this->realm) . '"'
					)
				);
			}

			$res->status = 401;
			$res->header['WWW-Authenticate'] = $this->type . ' ' . implode(',',$opt);
		}

		private function parseDigest()
		{
			$needed_parts = array(
				'nonce'    => 1,
				'nc'       => 1,
				'cnonce'   => 1,
				'qop'      => 1,
				'username' => 1,
				'uri'      => 1,
				'response' => 1
			);

			$data = array();
			$keys = implode('|',array_keys($needed_parts));

			preg_match_all(
				'@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@',
				$_SERVER['PHP_AUTH_DIGEST'],
				$matches,
				PREG_SET_ORDER
			);

			foreach($matches as $m){
				$data[$m[1]] = $m[3] ? $m[3] : $m[4];
				unset($needed_parts[$m[1]]);
			}

			return $needed_parts ? false : $data;
		}

		public function check($user,$pass)
		{
			switch($this->type){
				case 'Basic':
					return ($user === $this->user and $pass === $this->pass);

				case 'Digest':
					$data = $this->parseDigest();
					
					if($data === false)
						return false;

					$A1    = md5($user . ':' . $this->realm . ':' . $pass);
					$A2    = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
					$valid = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

					return ($data['response'] === $valid);
			}
		}
	}
?>