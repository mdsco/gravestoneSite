<?php

	require("database-querier.php");

	if($_REQUEST['action'] == 'write'){

		$customer_id = $_REQUEST['user_id'];

		if($customer_id == ''){

			$user_id = uniqid();
			$count_object = "{}";

		    $sqlInsert = 'INSERT INTO products_count(id, count_object) VALUES("' 
		    							. $user_id . '", "' . $count_object . '");';

			DatabaseQuerier::insertIntoDatabase($sqlInsert);

			$arr = array( 'user_id' => $user_id );

			echo json_encode($arr);

		}

	}

?>


