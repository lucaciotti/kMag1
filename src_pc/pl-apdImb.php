<?php 
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
// include("odbcSocketLib.php");
headx("TEST");

$db = getODBCSocket();

$collo = getNumeric('collo');

$id = $_GET['id'];
$id_pl = $_GET['id_pl'];
print("Letti parametri per numCollo $collo<br>");
$art = getString('art');
print("Letti parametri per codImb $art<br>");

$rep = $_GET['rep'];

$banc = getNumeric('bancnum');
print("Letti parametri per numBancale $banc<br>");

$codBanc = getString('codbanc');
print("Letti parametri per codBanc $codBanc<br>");

$pesocollo = getNumeric('pesocollo');
$pesobanc = getNumeric('pesobanc');

$imb_u_misural = getNumeric('imb_u_misural');
$imb_u_misurah = getNumeric('imb_u_misurah');
$imb_u_misuras = getNumeric('imb_u_misuras');

$pal_u_misural = getNumeric('pal_u_misural');
$altezza = getNumeric('altezza');
$pal_u_misuras = getNumeric('pal_u_misuras');

//Tolgo la eventuale virgola nel peso
$pesocollo = str_replace(",", ".", $pesocollo);
$pesobanc = str_replace(",", ".", $pesobanc);

if( $art != "") {
	$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, $collo, '', 1, '', '".$art."', '".$rep."', $banc, $altezza, $imb_u_misural, $imb_u_misurah, $imb_u_misuras, $pesocollo )";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}
	//Impegno il collo
	$Query = "insert into u_termpl (id_term, id_pl, collo) values (1111111, $id_pl, $collo)";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}
}

if( $codBanc != "") {
	$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, 0, '', 1, '', '".$codBanc."', '".$rep."', $banc, $altezza, $pal_u_misural, 0, $pal_u_misuras, $pesobanc )";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}
	$Query = "insert into u_bancpl (reparto, id_pl, bancale) values ('@SPED', $id_pl, $banc)";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}
}

print("<br> <h3>Aggiornamento Completato</h3>");

?>
<input type="button" value="Chiudi Pagina" onclick="self.close()">