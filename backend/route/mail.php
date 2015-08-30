<?php
	namespace route;

	class RouteMail extends RouteBase {
		public function route(){
			$this->assign('ControllerMail')
				->on('list','getMailLis')
				->on('write/:to','writeNewMail')
				->on('/:name','defaultAction');
		}
	}
?>