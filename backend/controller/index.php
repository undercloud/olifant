<?php
	namespace olifant\controller;

	use olifant\http\External;
	use olifant\http\Upload;
	use stdClass;

	class ControllerIndex extends ControllerBase
	{
		public function index($req,$res)
		{

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

		public function upload($req,$res)
		{
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if($req->overflow){
					die('overflow');
				}

				if(isset($req->files['myfile'])){
					foreach($req->files['myfile'] as $file){
						$u = new Upload($file);

						/*$u->mount($_SERVER['DOCUMENT_ROOT'] . '/hold')
						  ->mode(0555);*/

						try{
							$data = $u->move();
							var_dump($data);
						}catch(Exception $e){
							var_dump($e);
						}
					}
				}
			}else if($_SERVER['REQUEST_METHOD'] == 'GET'){
				echo '
				<form method="post" target="/upload" enctype="multipart/form-data">
					<input type="file" name="myfile[]" multiple />
					<button type="submit">Upload</button>
				</form>';
			}
		}
	}
?>