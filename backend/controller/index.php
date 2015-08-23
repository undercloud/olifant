<?php
	namespace controller;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{
			$res->body = '<h1 style="font-size:250px">200 OK</h1>';

			return $res;
		}
	}
?>