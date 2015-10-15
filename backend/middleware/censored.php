<?php
	namespace olifant\middleware;

	class MiddlewareCensored extends MiddlewareBase
	{
		public $path = array(
			'/гандурас/сука',
		);

		public function handle(&$req,&$res,&$call)
		{
			$res->body = 'Ты ' . $res->body;
		}
	}
?>