<?php
	namespace olifant\http;

	use olifant\http\RequestBuilder;

	class Upload
	{
		private $options = array();
		private $files   = array();
		private $filter  = array();

		/*
			mount
			mode

			nest
			node
			name

		*/

		public function __construct(RequestBuilder $req)
		{
			$this->files = $req->files;
		}

		public function setFilter(array $filter = array())
		{
			$this->filter = $filter;
			return $this;
		}

		private function createPath()
		{
			$hash = md5(uniqid(time(),true));

			$dir_len  = $this->options['node'];
			$dir_nest = $this->options['nest'];
			
			$path_size = $dir_len * $dir_nest;

			if(0 === $path_size)
				return array();

			$path = substr($hash,0,$path_size);	
			$path = str_split($path,$dir_len);

			return $path;
		}

		private function resolvePath(array $path)
		{
			array_unshift(
				$path,
				$this->options['mount']
			);

			$path = implode(DIRECTORY_SEPARATOR,$path);
			if(file_exists($path)){
				return true;
			}

			return mkdir(
				$path,
				$this->options['mode'],
				true
			);
		}

		public function handle($call)
		{
			foreach($this->files as $file){
				$status = array();
				if(is_uploaded_file($file['tmp_name'])){
					if($file['error'] == UPLOAD_ERR_OK){
						$path = $this->createPath();
						if($this->resolvePath($path)){
							$ext = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

							if(0 == count($this->filter) or in_array($ext,$this->name)){
								$dest = $this->options['mount'] . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR,$path) . DIRECTORY_SEPARATOR;
								$dest .= olifant\fn\random($this->options['name']);
								
								if($ext){
									$dest .= '.' . $ext;
								}
									
								if(move_uploaded_file($file['tmp_name'],$dest)){
									$call($status);
								
								}
						}
					}
				}
			}
		}
	}
?>

<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$storage = new Storage(array(
			'mount' => __DIR__ . DIRECTORY_SEPARATOR . 'hold',
			'mode' => '0777',
			'nest' => 0,
			'node' => 0,
			'name' => 12
		));

		$storage->setQueue($reorder['myfile']);
		$storage->handle(function($item){
			var_dump($item);
		});
	}else if($_SERVER['REQUEST_METHOD'] == 'GET'){
		?>

		<form method="post" target="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
			<input type="file" name="myfile"/>
			<button type="submit">Upload</button>
		</form>

		<?php
	}
?>