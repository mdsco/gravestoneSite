<?php

	require("database-querier.php");

	if ($_REQUEST['action'] == 'write') {

		$key = $_REQUEST['key'];
		$user_id = $_REQUEST['user_id'];

		$countArray = array();

		error_log("remove-letter is passed in: " . $user_id);

		$sql = "SELECT * FROM products_count WHERE id = '" . $user_id . "';";
		$result = DatabaseQuerier::queryDatabase($sql);

		if ($result) {

			while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			
				$count_object_from_db = $row['count_object'];
			
				$countArray = json_decode($count_object_from_db, true);

				unset($countArray[$key]);

				$json_object = json_encode($countArray);

				$sqlInsert = "UPDATE products_count SET count_object = '"
					 . $json_object . "' WHERE id = '" . $user_id . "';";

				DatabaseQuerier::insertIntoDatabase($sqlInsert);

			}

		}
	}

?>