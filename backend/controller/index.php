<?php
	namespace olifant\controller;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{
			$res->file->contents = 'Hello';
			$res->file->name = 'World.txt';
			$res->file->header = array(
				'Content-Length' => 1
			);


			return $res;
		}
	}
?>