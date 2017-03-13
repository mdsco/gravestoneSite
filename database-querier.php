<?php 

	class DatabaseQuerier {

		public static function insertIntoDatabase($sqlInsert){

			try {

				$connection = DatabaseQuerier::prepareDatabase();
				$connection->exec($sqlInsert);
				$connection = null;

		    } catch (PDOException $e) {
		        die("DB ERROR: ". $e->getMessage());
		    }

		}

		public static function queryDatabase($sqlQuery){

			try{

				$connection = DatabaseQuerier::prepareDatabase();
	    		$result = $connection->query($sqlQuery);
				$connection = null;

				return $result;

			} catch(PDOException $e){
				error_log("Whats error?   " . $e);
			}
		}

		private static function prepareDatabase(){

			$host="localhost"; 

			$root="root"; 
			$root_password="Letmein2MySQL"; 

			$user='newuser';
			$pass='newpass';
			$db= 'letter_counter_db'; 
			$count_table = 'products_count';
			$current_user_table = 'current_user_id';

			$connection = new PDO("mysql:host=$host", $root, $root_password);

	        $connection->exec("CREATE DATABASE IF NOT EXISTS `$db`;
	                CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
	                GRANT ALL ON `$db`.* TO '$user'@'localhost';") 
	        or die(print_r($connection->errorInfo(), true));

		    $sql = "USE `$db`;";
			$connection->exec($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `$count_table`(`id` varchar(40) NOT NULL, `count_object` varchar(10000) NOT NULL, PRIMARY KEY(`id`));";
			$connection->exec($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `$current_user_table`(`id` varchar(1) NOT NULL, `user_id` varchar(35) NOT NULL, PRIMARY KEY(`id`));";
			$connection->exec($sql);

			$sql = "INSERT INTO `$current_user_table`(`id`, `user_id`) VALUES('1', '0000000000');";
			$connection->exec($sql);

			return $connection;

		} 

	}

?>