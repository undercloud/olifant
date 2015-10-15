<?php
	namespace olifant\route;

	class RouteRus extends RouteBase
	{
		public function route()
		{
			$this->on('/сука',function($req,$res){
				$res->body = 'Сука';

				return $res;
			});
		}
	}
?>