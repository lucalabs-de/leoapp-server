<?php

	require_once('../../dbconfig.php');
	require('../secure.php');

	$db = new mysqli(dbhost, dbuser, dbpass, dbname);

	if ($db->connect_error)
		die("-ERR db");

	if (!isSecure($_SERVER['AUTHENTICATION'], $db))
		http_response_code(401);

	$adressat = $_GET['to'];
	
	$url = 'null';

	if($adressat == "")
		die("-ERR m");

	if($adressat == "Sek1") {
		$adressat = "Sek I";
	} else if($adressat == "Sek2") {
		$adressat = "Sek II";
	} else if($adressat == "Alle") {
		$adressat = "Alle";
	}

	$heute = date("Y-m-d H:i:s");
	$titel = $db->real_escape_string($_GET['title']);
	$inhalt = $db->real_escape_string($_GET['content']);
	$ablaufdatum = $db->real_escape_string($_GET['date']);

	$titel = str_replace("_ae_", "ä", $titel);
	$titel = str_replace("_oe_", "ö", $titel);
	$titel = str_replace("_ue_", "ü", $titel);

	$titel = str_replace("_Ae_", "Ä", $titel);
	$titel = str_replace("_Oe_", "Ö", $titel);
	$titel = str_replace("_Ue_", "Ü", $titel);

	$titel = str_replace("_ss_", "ß", $titel);

	$inhalt = str_replace("_ae_", "ä", $inhalt);
	$inhalt = str_replace("_oe_", "ö", $inhalt);
	$inhalt = str_replace("_ue_", "ü", $inhalt);

	$inhalt = str_replace("_Ae_", "Ä", $inhalt);
	$inhalt = str_replace("_Oe_", "Ö", $inhalt);
	$inhalt = str_replace("_Ue_", "Ü", $inhalt);

	$inhalt = str_replace("_ss_", "ß", $inhalt);

	
	if($titel==""||$inhalt==""||$ablaufdatum=="")
		die("-ERR m");

	$query = "INSERT INTO Eintraege VALUES (null, 0, '".$adressat."', '".$titel."', '".$inhalt."', 'null', '".$heute."', '".$ablaufdatum."')";


	$result = $db->query($query);
	if ($result === false) {
		die("-ERR db");
	}

	echo "+OK";

	$db->close();

?>