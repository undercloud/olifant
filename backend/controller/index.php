<?php
	namespace olifant\controller;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{
			/*$req->auth->realm = 'KHazarskaya';
			$req->auth->type = 'Digest';

			if($req->auth->login == false){
				$req->auth->ask($res);
				$res->body = "<h1>CANCELLED</h1>";
			}else{
				if($req->auth->check('foo','bar')){
					echo 'Okay';
				}else{
					echo 'Fail';
				}
			}*/

			//$res->refreshUrl = 'video';
			//$res->refreshTimeout = 3;

			//$res->body = 'Wait...';

			new \olifant\model\ModelBase();
			new \olifant\model\nested\ModelLalka();
			
			//echo $ebal;

			return $_SERVER;
		}

		public function video($req,$res)
		{
			if(!$req->params['id'])
				$req->params['id'] = '13810183';

			$res->body = 'Ok';

			return $res;
		}

		public function etc($req,$res)
		{
			$res->body = $_SERVER;

			return $res;
		}
	}
?>