<?php

	require_once('../dbconfig.php');

	$db = new mysqli(dbhost, dbuser, dbpass, dbname);

	if ($db->connect_error)
    	die("-connection failed: ".$db->connect_error);

	$id = $db->real_escape_string($_GET['id']);

	$sql = "DELETE FROM Eintraege WHERE EintragID=".$id;

	$result = $db->query($sql);

	if ($result === false)
		die "-ERR";

	echo "+OK";


?>