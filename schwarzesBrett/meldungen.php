<?php

	require_once('../dbconfig.php');

	$db = new mysqli(dbhost, dbuser, dbpass, dbname);

	if ($db->connect_error)
    	die("-connection failed: ".$db->connect_error);

	$date = date("Y-m-d");

	$query = "SELECT EintragID, Anhang, Gelesen, Titel, Adressat, Inhalt, UNIX_TIMESTAMP(Erstelldatum) as Erstell, UNIX_TIMESTAMP(Ablaufdatum) as Ablauf FROM Eintraege WHERE Ablaufdatum >= '".$date."' ORDER BY Erstell DESC";

	$result = $db->query($query);
	if ($result !== false) {
		while ($row = $result->fetch_assoc()) {
				echo $row['Titel'] . ";" . $row['Adressat'] . ";" . $row['Inhalt'] . ";" . $row['Erstell'] . ";" . $row['Ablauf']. ";" . $row['EintragID'] . ";" . $row['Gelesen'] . ";" . $row['Anhang'] . "_next_";
		}
	} else {
		$db->error;
		die("-error in query");
	}

	$db->close();

?>
