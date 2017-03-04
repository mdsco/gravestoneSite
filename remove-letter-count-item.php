<?php

	if ($_REQUEST['action'] == 'write') {

		$key = $_REQUEST['key'];	

		$countArray = array();

		$file_exists = file_exists('letter-count-log.txt');
		$letter_count_object;

		if ($file_exists) {

			$letter_count_object = file_get_contents('letter-count-log.txt');

			$countArray = json_decode($letter_count_object, true);

			unset($countArray[$key]);

			$json_object = json_encode($countArray);

        	file_put_contents('letter-count-log.txt', $json_object);
    	}
	}

?>