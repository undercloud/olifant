<?php
	\olifant\Settings::getInstance()
		->addSection('system')
			//->set('devmode','debug')
			->set('errlog',true)
			//->set('time_limit',30)
			//->set('memory_limit','128M')

		->addSection('app')

			->set('name','MyApp')
		
		->addSection('time')
			->set('zone','UTC')

?>