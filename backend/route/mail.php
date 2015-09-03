<?php
	namespace olifant\route;

	class RouteMail extends RouteBase {
		public function route(){
			$this->assign('ControllerMail')
				->on('list','getMailLis')
				->on('write/:to/:more/:shalavison','writeNewMail')
				->on('/:name','defaultAction');
		}
	}
?>