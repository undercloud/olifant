<?php
	namespace olifant\kernel;

	class ErrorHandler
	{
		public static function log($msg)
		{
			if(false === is_scalar($msg)){
				$msg = self::getDump($msg);
			}

			$msg = str_replace("\0", "",$msg);

			error_log($msg);
		}

		private static function getDump($obj)
		{
			ob_start();
			var_dump($obj);
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		private static function getSource($file,$line)
		{
			$start  = $line - 5;
			if($start < 0)
				$start = 0;

			$length = 9;

			$range = array_slice(file($file),$start,$length,true);

			$code = '
			<style>
				.olifant-error {
					position: relative;
					font-family: monospace;
					padding: 0 15px;
				}

				.olifant-error .item {
					padding: 15px 0;
					border-bottom: 1px solid #eee;
				}

				.olifant-error .item pre { 
					color: #2E3B47;
					border-left: 2px solid #05ad97;
					padding-left: 15px;
					display: block;
					box-sizing: border-box;
					max-width: 100%;
					overflow: auto;
				}

				.olifant-error h1 {
					color: #F22613;
					font-family: Arial;
					font-weight: normal;
					margin: 0;
					margin-top: 15px;
				}

				.olifant-error .path {
					line-height: 40px;
					font-family: monospace;
					color: #2d93c6;
					font-weight: bold;
					font-size: 12px;
				}

				.olifant-error .callable {
					color: #8E44AD;
					font-weight: bold;
				}

				.olifant-error .source-code {
					display: block;
					font-family: monospace;
					overflow: auto;
				}

				.olifant-error .source-code .line {
					display: block;
					white-space: nowrap;
				}

				.olifant-error .source-code .line.current {
					background-color: #e4F5e1; 
				}

				.olifant-error .source-code .line .line-no {
					text-align: right;
					width: 32px;
					display: inline-block;
					color: #b3b3b3;
					border-right: 1px solid #dad9d9;
					padding-right: 3px;
				}
				
				.olifant-error .source-code .line.current .line-no {
					font-weight: bold;
				}
			</style>';

			$code .= '<span class="source-code">';
			foreach($range as $l=>$c){
				$code .= '<span class="line' . ($l+1 == $line ? ' current' : '') .'"><span class="line-no">' . ($l + 1) . '</span><span>' . str_replace(array('&lt;?php','?&gt;'),'',highlight_string('<?php ' . $c . '?>',true)) . '</span></span>';
			}

			$code .= '</span>';

			return $code;
		}

		private static function parseStack(array $stack)
		{
			$echo = '';
			foreach($stack as $trace){
				$echo .= "<div class='item'>";

				if(isset($trace['file']))
					$echo .= '<div class="path">' . $trace['file'] . ':' . $trace['line'] . '</div>';
				
				if(isset($trace['class'])){
					$echo .= '<div class="callable">' . $trace['class'] . $trace['type'] . $trace['function'] . '</div>';
				}else if(isset($trace['function'])){
					$echo .= '<div class="callable">' . $trace['function'] . '</div>';
				}

				if(isset($trace['args']))
					$echo .= '<pre>' . htmlentities(self::getDump($trace['args']),ENT_QUOTES,'UTF-8') . '</pre>';
			
				$echo .= "</div>";
			}

			return $echo;
		}

		public static function handleError($errno, $errstr, $errfile, $errline)
		{
			$errors = array(
				1     => 'E_ERROR',
				2     => 'E_WARNING',
				4     => 'E_PARSE',
				8     => 'E_NOTICE',
				16    => 'E_CORE_ERROR',
				32    => 'E_CORE_WARNING',
				64    => 'E_COMPILE_ERROR',
				128   => 'E_COMPILE_WARNING',
				256   => 'E_USER_ERROR',
				512   => 'E_USER_WARNING',
				1024  => 'E_USER_NOTICE',
				2048  => 'E_STRICT',
				4096  => 'E_RECOVERABLE_ERROR',
				8192  => 'E_DEPRECATED',
				16384 => 'E_USER_DEPRECATED',
				30719 => 'E_ALL'
			);

			if(true === \olifant\Settings::get('system.errlog',false)){
				$msg = implode(' ',array(
					'PHP',
					str_replace('_',' ',ucfirst(strtolower(substr($errors[$errno],2)))) . ':',
					$errstr,
					'in',
					$errfile,
					'on line',
					$errline
				));

				self::log($msg);
			}

			if(!(error_reporting() & $errno)){
				return;
			}

			$echo = '<div class="olifant-error">' . 
					  '<div class="item">' . 
					    '<h1>' . $errors[$errno] . ': ' . $errstr . '</h1>' . 
					      '<div class="path">' . $errfile . '</div>' . 
					      self::getSource($errfile,$errline) .
					  '</div>' .
					  '<h1>Stack trace</h1>' . 
					  self::parseStack(debug_backtrace()) .
					'</div>';

			echo $echo;
			
			return true;
		}

		public static function handleException($e)
		{
			$msg   = $e->getMessage();
			$code  = $e->getCode();
			$file  = $e->getFile();
			$line  = $e->getLine();
			$trace = $e->getTrace();

			$echo = '<div class="olifant-error">' .
					  '<div class="item">' . 
					    '<h1>Exception: ' . $msg . ':' . $code . '</h1>' .
						  '<div class="path">' . $file . '</div>' .
						  self::getSource($file,$line) .
					  '</div>' . 
					  '<h1>Stack trace</h1>' . 
					  self::parseStack($trace) . 
					'</div>';

			echo $echo;
		}
	}
?>