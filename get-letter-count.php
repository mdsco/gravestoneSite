<?php

	if ($_REQUEST['action'] == 'write') {
		file_put_contents('letter-count-log.txt', $_REQUEST['char_count']);
	}

?>