<?php
	namespace route;

	class RouteIndex extends RouteBase {
		public function route(){
			$this->assign('ControllerIndex')
				 ->on('news','getAllNews')
				 ->on('news/sasai','getSasaiNews')
				 ->defaults('index');
		}
	}
?>