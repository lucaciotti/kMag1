<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
headx("TEST");

$collo = getNumeric('collo');
if($collo == 0) {
	header("location: pl_01_ask.php");
	exit();
}

$db = getODBCSocket();

$id = $_GET['id'];
$id_pl = $_GET['id_pl'];
print("Letti parametri per collo $collo<br>");
$art = getString('art');
print("Letti parametri per art $art<br>");
$prt = getNumeric('prt');
$prtBanc = getNumeric('prtbanc');
$artCollo = getString('artcollo');
$desCollo = getString('descollo');
$nColli = getNumeric('ncolli');
$rep = $_GET['rep'];
$hasbanc = isset($_GET['hasbanc']);
$banc = getNumeric('bancnum');
$banc = ($hasbanc ? $banc : 0);
$altezza = getNumeric('altezza');
$collopl = 0;
$codBanc = getString('codbanc');

$pesocollo = getNumeric('pesocollo');
$pesobanc = getNumeric('pesobanc');

$imb_u_misural = getNumeric('imb_u_misural');
$imb_u_misurah = getNumeric('imb_u_misurah');
$imb_u_misuras = getNumeric('imb_u_misuras');
$pal_u_misural = getNumeric('pal_u_misural');
$pal_u_misuras = getNumeric('pal_u_misuras');

$art2 = getString('art2');
print("Letti parametri per art2 $art2<br>");

$imb_u_misural2 = getNumeric('imb_u_misural2');
$imb_u_misurah2 = getNumeric('imb_u_misurah2');
$imb_u_misuras2 = getNumeric('imb_u_misuras2');
$pesocollo2 = getNumeric('pesocollo2');

$collopl = $collo + ($nColli < 2 ? 0 : 1);

//Tolgo la eventuale virgola nel peso
$pesocollo = str_replace(",", ".", $pesocollo);
$pesocollo2 = str_replace(",", ".", $pesocollo2);
$pesobanc = str_replace(",", ".", $pesobanc);

if( $art != "") {

	if($nColli < 2) {
		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, $collopl, '', 1, '', '".$art."', '".$rep."', $banc, $altezza, $imb_u_misural, $imb_u_misurah, $imb_u_misuras, $pesocollo )";
		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	} else {
		// ROBERTO 11.09.2013
		// anche il collo aggiuntivo ha un imballo...
		// Roberto 10.10.2014
		// adesso chiediamo peso e misure del secondo collo
		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, $collopl, '', 1, '', '".$art2."', '".$rep."', $banc, $altezza, $imb_u_misural2, $imb_u_misurah2, $imb_u_misuras2, $pesocollo2 )";
		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, $collo, '', 1, '', '".$art."', '".$rep."', $banc, $altezza, $imb_u_misural, $imb_u_misurah, $imb_u_misuras, $pesocollo )";
		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	}

	if($hasbanc) {
		// gestione bancale
		$Query = "update docrig set u_costk1 = $banc where riffromt = $id_pl and u_costk = $collo";
		if(trim($artCollo) != "") {
			$Query = "update docrig set u_costk1 = $banc where riffromt = $id_pl and (u_costk = $collo or u_costk = $collopl)";
		}
		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

		$Query = "update u_plmod set bancale = $banc where id = $id and collo = $collo";
		print("$Query<br>");
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

		if(isset($_GET['closebanc'])) {
			$artBanc = (isset($_GET['codbanc']) ? $_GET['codbanc'] : "" );
			$Query = "update docrig set u_traverso = $altezza where u_costk1 = $banc and riffromt = $id_pl";
			print("$Query<br>");
			if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

			$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
			$Query .= "values ($id, 1, 0, '', 1, '', '".$artBanc."', '".$rep."', $banc, $altezza, $pal_u_misural, 0, $pal_u_misuras, $pesobanc )";
			print("$Query<br>");
			if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
		}
	}

}

if( $prt > 0) {
    header("location: pl_06_selPrt.php?id=$id_pl&id_riga=$id&collo=$collo&ncolli=$nColli&artcollo=$artCollo&descollo=$desCollo&split=$split&prtbanc=$prtBanc&banc=$banc ");
} else {
	if( $prtbanc > 0) {
		// ho chiesto la stampa del bancale ma non del collo
		header("location: pl_07_prt.php?id=$id_pl&id_riga=$id&collo=0&ncolli=$nColli&artcollo=$artCollo&descollo=$desCollo&split=$split&prtbanc=$prtBanc&banc=$banc ");
	} else {
		header("location: pl_01_ask.php");
	}
}

?>
