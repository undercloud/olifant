<?php
	namespace olifant\controller;

	class ControllerError extends ControllerBase
	{
		public function notFound404($req,$res)
		{
			$res->status = 404;
			$res->body = '<h1>404</h1>';

			return $res;
		}

		public function forbidden403($req,$res)
		{
			$res->status = 403;
			$res->body = '<h1>403</h1>';

			return $res;
		}
	}
?>