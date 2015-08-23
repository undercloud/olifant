<?php
	\app\EventListener::getInstance()
		->on('app.run',function(){})
		->on('app.done',function(){});
?>