<?php
	namespace route;

	class RouteApp extends RouteBase
	{
		public function route()
		{
			$this->on('info',function(){ phpinfo(); })
				 ->on('index','ControllerIndex::index');
		}
	}
?>