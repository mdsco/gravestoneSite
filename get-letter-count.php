<?php

	require("database-querier.php");

	if ($_REQUEST['action'] == 'write') {

		$char_count = $_REQUEST['char_count'];
		$product_key = $_REQUEST['product_key']; 
		$user_id = $_REQUEST['user_id'];

		$countArray = array();

		$sql = "SELECT * FROM products_count WHERE id = '$user_id';";
		$result = DatabaseQuerier::queryDatabase($sql);

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$count_object_from_db = $row['count_object'];

	    	if($count_object_from_db == '{}'){
	    		
	    		$countArray['no_key'] = $char_count;    		
	    	
	    	} else{

				$countArray = json_decode($count_object_from_db, true);
				$countArray[$product_key] = $char_count;

			}
		}

		$json_object = json_encode($countArray);

		$sqlInsert = "UPDATE products_count SET count_object = '"
					 . $json_object . "' WHERE id = '" . $user_id . "';";

		DatabaseQuerier::insertIntoDatabase($sqlInsert);

		// $sql = "SELECT * FROM products_count;";
		// $result = DatabaseQuerier::queryDatabase($sql);

		// while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		// 	$outputString = "RESULT here for the money: " 
		// 			. $row['id']. ' - '. $row['count_object'] . "\n";

		// 	file_put_contents('output.txt', $outputString, FILE_APPEND);

		// }
	}

?>