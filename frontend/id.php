<?php
	for($i=0;$i<100;$i++){
		$hash = md5(uniqid());
		$name = dechex(crc32(substr($hash,6)));

		$first  = $hash[0] . $hash[1];
		$second = $hash[2] . $hash[3];
		$third  = $hash[4] . $hash[5];

		echo '/' . $first . '/' . $second . '/'  . $third . '/' .$name . PHP_EOL;
	}
?>