<?php
	namespace olifant\middleware;

	class MiddlewareCensored extends MiddlewareBase
	{
		public $path = array(
			'/',
			'etc'
		);

		public function handle(&$req,&$res,&$call)
		{
			
			echo 'yes';
		}
	}
?>