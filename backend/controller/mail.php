<?php
	namespace olifant\controller;

	class ControllerMail extends ControllerBase
	{
		public function defaultAction($req,$res)
		{
			var_dump($req);

			echo __METHOD__ . '@' . $req->params['name'];
		}

		public function writeNewMail($req,$res)
		{
			$res->status = 475;
			$res->statusText = 'Idinahui';

			return $res;
		}
	}
?>