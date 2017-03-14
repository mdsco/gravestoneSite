<?php

    require("database-querier.php");

    if(isset($_REQUEST['action'])) {

        if($_REQUEST['user_id'] !== NULL){
            $user_id_from_request = $_REQUEST['user_id']; 
            error_log("Type of user id   " . gettype($user_id_from_request));
        } else {
            error_log("User id is null");
        }


        if(!is_null($user_id_from_request)){
            
            error_log("USER ID FROM REQUEST: " . $user_id_from_request);

            $sqlInsert = "UPDATE current_user_id SET user_id = '"
                         . $user_id_from_request . "' WHERE id = '1';";

            DatabaseQuerier::insertIntoDatabase($sqlInsert);
        }
        
    }

?>