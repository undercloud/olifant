<?php
	namespace controller;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{
				
			$res->body = 'Index';

			return $res;
		}

		public function video($req,$res)
		{
			$res->body = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $req->params['id'] . '" frameborder="0" allowfullscreen></iframe>';
			return $res;
		}
	}
?>