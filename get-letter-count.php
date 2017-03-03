<?php

	if ($_REQUEST['action'] == 'write') {

		$char_count = $_REQUEST['char_count'];
		$product_key = $_REQUEST['product_key'];

		$countArray = array();

		$file_exists = file_exists('letter-count-log.txt');
		$letter_count_object;

		if ($file_exists) {
			$letter_count_object = file_get_contents('letter-count-log.txt');
        	file_put_contents($filename, 0);
    	}

		if($letter_count_object == '' || !$file_exists){
			$countArray['no_key'] = $char_count;
		}
		else{
			$countArray = json_decode($letter_count_object, true);
			$countArray[$product_key] = $char_count;
		}

		$json_object = json_encode($countArray);

		file_put_contents('letter-count-log.txt', $json_object);
	}

?>