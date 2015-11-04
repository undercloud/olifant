<?php
	namespace olifant\controller;

	use olifant\http\External;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{
			$ex = new External(
				'http://olifant.web/http/sasai/lalka',
				[
					'header' => [
						'foo' => 'bar'
					],
					'method' => 'post',
					'content' => [
						'x' => [1,2,3,5,0],
						'y' => [
							'k' => [1,2],
							'i' => 'bichoo'
						]
					],
					'content-type' => 'form',
					'files' => [
						[
							'name' => 'upload',
							'path' => $_SERVER['DOCUMENT_ROOT'] . '/favicon.ico'
						]
					]
				]
			);

			$ex->send();

			$headers = $ex->getHeaders();

			//$res->header['Content-Type'] = $headers['Content-Type'];
			//$ex->saveToFile($_SERVER['DOCUMENT_ROOT'] . '/cache.jpg');

			$res->body = $ex->getBody();

			return $res;
		}

		public function http($req,$res)
		{
			$req->query;
			$req->json;
			$req->header;
			$req->files;
			var_dump($req);

			return $res;
		}
	}
?>