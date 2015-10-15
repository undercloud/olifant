<?php
	namespace olifant\route;

	class RouteApp extends RouteBase
	{
		public function route()
		{
			$this
				->on('/','ControllerIndex::index')
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
				->on('/гандурас','RouteRus');
		}
	}
?>