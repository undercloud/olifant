<?php
	namespace controller;

	use \olifant\http\Router;
	use \olifant\http\Request;

	class ControllerBase
	{
		protected static function resolve($uri)
		{
			$ro = new Router(
				new Request(
					$uri
				)
			);

			return $ro->route();
		}

	}
?>