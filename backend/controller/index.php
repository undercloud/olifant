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

			new \olifant\http\Url();

			return $res;
		}

		public function video($req,$res)
		{
			if(!$req->params['id'])
				$req->params['id'] = 'QKvRYNqlo-M';

			$res->body = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $req->params['id'] . '" frameborder="0" allowfullscreen></iframe>';
			return $res;
		}
	}
?>