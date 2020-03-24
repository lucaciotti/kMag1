<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$anno = current_year();

headx($_POST["articolo"]);
$mode = strtoupper($_POST['mode']);

if($_POST["idriga"] != 0) {
  $Query = "select id_testa, codicecf from docrig where id = '" . $_POST["idriga"] . "'";
  try {
    if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	$id_testa = $db->getField(id_testa);
	$codicecf = $db->getField(codicecf);
  }
  catch(Exception $e) {
    echo 'Lettura id_testa: ' .$e->getMessage();
  }
  if($codicecf == "F01021") {
    // fornitore Krona, porto il materiale ai banchi
	/*
	  $Query = "update docrig set quantitare = 0 where id = " . $_POST["idriga"];
	  try {
		$rs1 = $conn->Execute($Query);
	  }
	  catch(Exception $e) {
		echo 'Azzeramento residuo: ' .$e->getMessage();
	  }
	*/
	  $Query = "update doctes set numrighepr = numrighepr -1 where id = $id_testa";
	  try {
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	  }
	  catch(Exception $e) {
		echo 'Aggiornamento righe prelevabili: ' .$e->getMessage();
	  }
	writeDocumento('OI','F01021',true);
  } else {
    // il fornitore � un terzista, gli mando il materiale
	writeDocumento('TL',$codicecf,false);
  }
} else {
	if("CP" == $mode) {
		// carico di produzione
		writeDocumento('CP','F01021', true);
	} else {
		// lettura cartellino; ordine implicito
		writeDocumento('OI','F01021',true);
	}
}
//	$ret = shell_exec("d:\arca\arca_italia\autorun-barcode.bat");
print("Documento caricato.\n");

if("CP" == $mode) {
	$lottopadre = $_POST["lottopadre"];
    print ("<br><br>Generato lotto: $lottopadre<br>");
	print ("Num. etichette: <input type=\"text\" id=\"num\">");
	print ("<input type=\"submit\" value=\"Stampa\" onclick=\"");
	print ("sendUrl('writeetich.php?codicearti=" . urlencode($_POST["padre"]) . "&lotto=$lottopadre&quantita='+document.getElementById('num').value)");
	print ("\"><br>");
	print ("<br/><a href=\"askcp.php\">Altra ricerca</a>\n");
} else {
	print ("<br/><a href=\"askdb.php\">Altra ricerca</a>\n");
}
footer();


function writeDocumento($tipoDoc, $codiceCF, $internal) {
	global $conn, $anno;

	$id_testa = time();
	$Query = "insert into u_bardt ";
	$Query .= "(id, datadoc, codicecf, tipodoc, numerodoc, magpartenz) VALUES ( ";
	$Query .= "$id_testa, ";
	$Query .= "{^" . date("Y-m-d") . "}, ";
	$Query .= "'".$codiceCF."', ";
	$Query .= "'".$tipoDoc. "', '', '" . CONFIG::$DEFAULT_MAG . "' )";
	try {
	  if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	}
	catch(Exception $e) {
	  echo 'Inserimento testa: ' .$e->getMessage();
	}

	$count = strtoupper($_POST['count']);
	$id = ($id_testa % 1000000)*100;
	if($internal) {
		$Query = "insert into u_bardr ";
		$Query .= "(id, id_testa, espldistin, datadoc, codicecf, tipodoc, codicearti, quantita, lotto, numerodoc, magpartenz) VALUES ( ";
		$Query .= "$id, ";
		$Query .= "$id_testa, ";
		$Query .= ($count > 0 ? "'P', " : "' ', ");
		$Query .= "{^" . date("Y-m-d") . "}, ";
		$Query .= "'".$codiceCF."', ";
		$Query .= "'".$tipoDoc."', ";
		$Query .= "'". $_POST["padre"] ."', '";
		$Query .= $_POST["quantita"] . "', ";
		$Query .= "'" . $_POST["lottopadre"] . "', ";
		$Query .= "'', '" . CONFIG::$DEFAULT_MAG . "') ";
		try {
			if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		}
		catch(Exception $e) {
			echo 'Inserimento riga padre: ' .$e->getMessage();
		}
		$id++;
	}

	for($j = 1; $j <= $count; $j++) {
		$Query = "insert into u_bardr ";
		$Query .= "(id, id_testa, espldistin, datadoc, codicecf, tipodoc, codicearti, quantita, lotto, numerodoc, magpartenz) VALUES ( ";
		$Query .= "$id, ";
		$Query .= "$id_testa, ";
		$Query .= "'C', ";
		$Query .= "{^" . date("Y-m-d") . "}, ";
		$Query .= "'".$codiceCF."', ";
		$Query .= "'".$tipoDoc."', ";
		$Query .= "'". $_POST["code$j"] ."', '";
		$Query .= $_POST["qta$j"] . "', ";
		$Query .= "'" . $_POST["lotto$j"] . "', ";
		$Query .= "'', '" . CONFIG::$DEFAULT_MAG . "' ) ";
	  try {
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	  }
	  catch(Exception $e) {
		echo 'Inserimento righe: ' .$e->getMessage();
	  }
	  $id++;
	}

}

?>
