<?php
	namespace olifant\http;

	class JSON
	{
		private static $depth = 512;

		public static function decode($data, $assoc = false)
		{
			$decoded = json_decode($data, $assoc, self::$depth);

			return self::checkError($decoded);
		}

		public static function encode($data)
		{
			$encoded = json_encode(
				$data,
				JSON_HEX_TAG  | 
				JSON_HEX_AMP  | 
				JSON_HEX_APOS | 
				JSON_HEX_QUOT | 
				JSON_FORCE_OBJECT,
				self::$depth
			);

			return self::checkError($encoded);
		}

		public static function checkError($data)
		{
			$last_error = json_last_error();

			if($last_error === JSON_ERROR_NONE)
				return $data;
			else
				throw new AppException(
					'JSON encoding error ' . 
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