<?php

	require_once('../../dbconfig.php');
	require('../_fpdf/fpdf181/fpdf.php');

	$db = new mysqli(dbhost, dbuser, dbpass, dbname);

	if ($db->connect_error)
		die("-ERR db");

	$toArr = $_POST['to'];

	$data = $_POST['addon'];
	$fileName = $_POST['filename'];

	if($fileName != "") {
		$serverFile = date().$fileName;
		$fp = fopen('../uploads/'.$serverFile,'w');
		$url = "/schwarzes_brett/uploads/".$serverFile;
		$date_decoded = base64_decode(substr($data, 28));

		fwrite($fp, $date_decoded);
		fclose($fp);
	} else {
		$url = 'null';
	}

	if($toArr == "")
		die("-ERR m");

	$printAdr = "";

	foreach ($toArr as $adressat) {
		if($adressat == "sek1") {
			$adressat = "Sek I";
		} else if($adressat == "sek2") {
			$adressat = "Sek II";
		} else if($adArray == "Alle") {
			$adressat = "Alle";
		}

		$heute = date("Y-m-d H:i:s");
		$titel = $db->real_escape_string($_POST['title']);
		$inhalt = $db->real_escape_string($_POST['content']);
		$ablaufdatum = $db->real_escape_string($_POST['date']);

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
		$inhalt = str_replace('\n', '\\\\\\\\', $inhalt);

		$inhalt = str_replace("_Ae_", "Ä", $inhalt);
		$inhalt = str_replace("_Oe_", "Ö", $inhalt);
		$inhalt = str_replace("_Ue_", "Ü", $inhalt);

		$inhalt = str_replace("_ss_", "ß", $inhalt);

		if($titel==""||$inhalt==""||$ablaufdatum=="")
			die("-ERR m");

		$query = "INSERT INTO Eintraege VALUES (null, 0, '".$adressat."', '".$titel."', '".$inhalt."', '".$url."' , '".$heute."', '".$ablaufdatum."')";

		$result = $db->query($query);
		if ($result === false) {
			echo $db->error;
			die("-ERR db");
		}
		
		if (preg_match("/[5-9]/", $adressat) === 1)
			$printAdr .= "Klasse ";
		
		$printAdr .= $adressat.", ";

	}

	echo "+OK ";

	$name = '_cached_pdfs/MeldungSchwarzesBrett'.date("Y-m-d-H:i").'.pdf';

	$files = glob('../_cached_pdfs');
	foreach($files as $file){
		if(is_file($file))
			unlink($file);
	}

	$pdf = new FPDF();
	$pdf->AddPage();

	$pdf->SetFont('Arial', 'BU', 25);
	$pdf->Cell(40, 10, rtrim($printAdr, ', '));
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 10, date("d.m.Y"), 0, 0, 'R');
	$pdf->Ln(15);

	$pdf->SetFont('Arial', 'B', 18);
	$pdf->MultiCell(0, 10, $titel);
	$pdf->Ln(10);

	$pdf->SetFont('Arial', '', 14);
	$inhalt = str_replace('\\\\\\\\', "\n", $inhalt);
	$inhalt = str_replace("\\", "", $inhalt);
	$pdf->MultiCell(0, 8, $inhalt);
	
	$pdf->Ln();
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(0, 10, 'Relevant bis '.date("d.m.Y", strtotime($ablaufdatum)), 0, 0, 'R');

	$pdf->Output('F', '../'.$name);

	echo $name;

	$db->close();

?>
