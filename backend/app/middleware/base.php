<?php
	namespace olifant\middleware;

	abstract class MiddlewareBase
	{
		abstract public function handle(&$req, &$res, &$call);
	}
?>