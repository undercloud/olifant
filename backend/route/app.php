<?php
	namespace route;

	class RouteApp extends RouteBase
	{
		public function route()
		{
			$this
				->on('/','ControllerIndex::index')
				->on('params/:name',function($req,$res){

					if(!$req->params['name'])
						$req->params['name'] = 'Unknown';

					echo "<h1>Hello {$req->params['name']}</h1>";
				})
				->on('mail','RouteMail')
				->on('video/:id','ControllerIndex::video');
		}
	}
?>