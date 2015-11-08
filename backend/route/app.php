<?php
	namespace olifant\route;

	class RouteApp extends RouteBase
	{
		public function route()
		{
			$this
				->on('/index/:name/:surname','ControllerIndex::index')
				->on('/album\_[0-9]*/:bebeleh/:mabeleh',function($req,$res){

					var_dump($req);

					$res->body = "<h1>№" . str_replace('album','',reset($req->params)) ."</h1>";

					return $res;
				})
				->on('/norx/:sasai/:lalka',function($req,$res){

					var_dump($req);
				})
				->on('/sasai/lalka',function($req,$res){

					var_dump($req);
				})
				->on('/гандурас','RouteRus')
				->on('/cli',function(){
					$cli = new \olifant\Cli();

					$prompt = $cli->highlight('Enter your name:','white');
					$name = $cli->read($prompt);

					$cli->write('Hello ' . $name);
				})
				->on('/js',function(){

					return '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>';
				})
				->on('/http','ControllerIndex::http')
				->on('/upload','ControllerIndex::upload');
		}
	}
?>