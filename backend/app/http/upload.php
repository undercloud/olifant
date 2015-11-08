<?php	
	namespace olifant\http;

	use olifant\exceptions\AppException;

	class Upload
	{
		private $file     = array();
		private $mount    = null;
		private $mode     = 0777;
		private $nest     = 0;
		private $node     = 0;
		private $size     = 0;
		private $filter   = array();
		private $exfilter = array();
		private $random   = 12;

		public static $errors = array(
			UPLOAD_ERR_OK         => 'There is no error, the file uploaded with success',
			UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
			UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
			UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
			UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
			UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
		);

		public function __construct(array $file)
		{
			$file['ext'] = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

			$this->file = $file;
		}

		public function mount($mount)
		{
			$this->mount = rtrim($mount,' \\/');

			return $this;
		}

		public function mode($mode)
		{
			$this->mode = $mode;

			return $this;
		}

		public function size($size)
		{
			$this->size = $size;

			return $this;
		}

		public function filter(array $filter = array())
		{
			$this->filter = $filter;

			return $this;
		}

		public function excludeFilter(array $exfilter = array())
		{
			$this->exfilter = $exfilter;

			return $this;
		}

		public function subPath($node,$nest)
		{
			$this->node = $node;
			$this->nest = $nest;

			return $this;
		}

		public function move($name = null)
		{
			if(null == $this->mount)
				throw new AppException('Mount folder is not defined', 1001);
			
			if(UPLOAD_ERR_OK != $this->file['error'])
				throw new AppException(
					self::$errors[(int)$this->file['error']],
					(int)$this->file['error']
				);

			if(false == is_uploaded_file($this->file['tmp_name']))
				throw new AppException('File upload is not by HTTP request', 1002);

			if($this->size and $this->size < (int)$this->file['size'])
				throw new AppException('The uploaded file exceeds the ' . $this->size . ' bytes', 1003);

			if(
				($this->filter   and false == in_array($this->file['ext'],$this->filter)) or
				($this->exfilter and true  == in_array($this->file['ext'],$this->exfilter))
			)
				throw new AppException('File extension ' . $this->file['ext'] . ' disabled', 1004);

			$subpath = false;
			if($this->node and $this->nest){
				$hash = md5(uniqid(time(),true));
				
				$path_size = $this->node * $this->nest;

				$path = substr($hash,0,$path_size);	
				$path = str_split($path,$this->node);

				$subpath = implode(DIRECTORY_SEPARATOR,$path);
				
				array_unshift($path,$this->mount);

				$path = implode(DIRECTORY_SEPARATOR,$path);
				
				if(false == file_exists($path)){
					if(false == @mkdir($path,$this->mode,true)){
						throw new AppException('Can\'t create path ' . $path, 1005);	
					}
				}
			}

			if(null === $name)
				$name = \olifant\fn\random($this->random) . '.' . $this->file['ext'];

			$fullpath = (
				$this->mount .
				(
					(false !== $subpath)
					? (DIRECTORY_SEPARATOR . $subpath)
					: ''
				) .
				(DIRECTORY_SEPARATOR . $name)
			);
			
			if(false == move_uploaded_file($this->file['tmp_name'], $fullpath))
				throw new AppException('Can\'t move uploaded file from ' . $this->file['tmp_name'] . ' to ' . $fullpath, 1006);
			
			return array(
				'fullpath'  => $fullpath,
				'subpath'   => (false !== $subpath ? (DIRECTORY_SEPARATOR . $subpath) : false),
				'name'      => $name,
				'extension' => $this->file['ext'], 
				'size'      => $this->file['size'],
				'mime'      => $this->file['type']
			);
		}
	}
?>