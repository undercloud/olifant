<?php
	namespace controller;

	class ControllerError extends ControllerBase
	{
		public function notFound404($req,$res)
		{
			$res->status = 404;
			$res->statusText = 'Page Not Found You Looser';

			$res->body = '<h1>404</h1>';

			return $res;
		}
	}
?>