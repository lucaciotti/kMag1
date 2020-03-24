<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

head("Caricamento inventario da Excel");

$err = false;
if ($_FILES["file"]["type"] == "text/xml") {
	if ($_FILES["file"]["error"] > 0) {
	   echo "Errore: " . $_FILES["file"]["error"] . "<br>";
	   $err = true;
	} 
} else {
	echo "Errore: File di tipo non corretto<br>";
	$err = true;
}

if(!$err) {
	$xml = DOMDocument::load($_FILES["file"]["tmp_name"]); 
	$list = $xml->getElementsByTagName("Worksheet"); 
	if($list->length != 1) {
		echo "Errore: File non riconosciuto";
		$err = true;
	}
}

if(!$err) {
	$rowslist = $xml->getElementsByTagName("Row"); 
	if($rowslist->length < 2) {
		echo "Errore: il file non contiene dati";
		$err = true;
	}
}

if(!$err) {
	$cellslist = $rowslist->item(0)->getElementsByTagName("Cell"); 
	if($cellslist->length < 5) {
		echo "Errore: mancano alcune colonne";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(0)) != "Magazzino") {
		echo "Errore: colonna 'Magazzino' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(1)) != "Codice") {
		echo "Errore: colonna 'Codice' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(3)) != "Quantita") {
		echo "Errore: colonna 'Quantita' non trovata";
		$err = true;
	}
}

if(!$err) {
	if(getCellValue($cellslist->item(4)) != "Lotto") {
		echo "Errore: colonna 'Lotto' non trovata";
		$err = true;
	}
}

if(!$err) {
	print("<table>\n<tr>\n<th>Magazzino</th>\n<th>Codice</th>\n<th>Descrizione</th>\n<th>Quantita</th>\n<th>Lotto</th>\n</tr>\n");
	for( $j = 1; $j < $rowslist->length; $j++) {
		$cellslist = $rowslist->item($j)->getElementsByTagName("Cell"); 
		if($cellslist->length <4) {
			echo "<tr><td colspan=\"5\">Riga incompleta</td></tr>";
			$err = true;
			continue;
		}
		
		$maga = getCellValue($cellslist->item(0));
		$Query = "SELECT DESCRIZION FROM MAGANA WHERE CODICE = '".$maga."'";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
		if(!$db->EOF) {
			$descm = $db->getField(descrizion);
		} else {
			$descm = "Magazzino non trovato!";
			$err = true;
		}

		$codice = getCellValue($cellslist->item(1));
		$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = '".$codice."'";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
		if(!$db->EOF) {
			$desc = $db->getField(descrizion);
		} else {
			$desc = "Articolo non trovato!";
			$err = true;
		}
		
		$quantita = getCellValue($cellslist->item(3));
		
		if($cellslist->length > 4) {
			$lotto = getCellValue($cellslist->item(4));
		} else {
			$lotto = "&nbsp;";
		}
		
		print("<tr>\n<td>$maga-$descm</td>\n<td>$codice</td>\n<td>$desc</td>\n<td>$quantita</td>\n<td>$lotto</td>\n</tr>\n");
	}
	print("</table>\n");
}   

print("<br>\n");
if($err) {
	print("Sono presenti errori: correggerli e reinviare il file.\n");
} else {
    //rifacciamo il giro importando veramente
	for( $j = 1; $j < $rowslist->length; $j++) {
		$cellslist = $rowslist->item($j)->getElementsByTagName("Cell"); 
		$maga = getCellValue($cellslist->item(0));
		$codice = getCellValue($cellslist->item(1));
		$quantita = getCellValue($cellslist->item(3));
		if($cellslist->length > 4) {
			$lotto = getCellValue($cellslist->item(4));
		} else {
			$lotto = "";
		}
		$Query = "insert into u_invent (codicearti, quantita, magazzino, lotto, timestamp, id_term) values (";
		$Query .= "'".$codice."', $quantita, '".$maga."', '".$lotto."', datetime(), 0)";
//		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
	}
	print("Dati importati.\n");
}
print("<br>\n");
goMain();
footer();

function getCellValue($doc) {
	if( $doc->getElementsByTagName("Data")->length > 0) { 
        return $doc->getElementsByTagName("Data")->item(0)->nodeValue;
	} else {
		return '';
	}
}
?>