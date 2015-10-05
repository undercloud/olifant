<?php
	namespace olifant\route;

	class RouteApp extends RouteBase
	{
		public function route()
		{
			$this
				->on('/','ControllerIndex::index')
				->on('/params/:name',function($req,$res){
					if(!$req->params['name'])
						$req->params['name'] = 'Unknown';

					echo "<h1>Hello {$req->params['name']}</h1>";
				})
				->on('/mail','RouteMail')
				->on('/video/:id','ControllerIndex::video')
				->on('/etc','ControllerIndex::etc')
				->on('/cli',function($req,$res){
					$cli = new \olifant\CLI();

					foreach($hui as $h);

					$input = $cli->read($cli->highlight('Enter your name:','purple'));

					$cli->write($cli->args('sasai') + 10);

					$cli->process('cd /var/www');

					var_dump(
						$cli->process(
							'du',
							array(
								'/var/www/',
								'--si',
								'--max-depth=1'
							)
						)
					);

				});
		}
	}
?>