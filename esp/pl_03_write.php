<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = $_POST['id'];
$id_pl = $_POST['id_pl'];
$qta = $_POST['qta'];
$collo = ($_POST['collo'] == "" ? 0 : $_POST['collo']);
//TORNO INIZIO...NO COLLO!!!
if($collo == 0) {
	header("location: pl_01_ask.php");
	exit();
}

$anno = current_year();
$db = getODBCSocket();
$dbWrite = getODBCSocket();
$db2 = getODBCSocket();

$lotto = trim($_POST['lotto']);
$prt = ( $_POST['print'] == "" ? 0 : $_POST['print']);
$newfatt = ($_POST['newFatt'] == 0 ? 1 : $_POST['newFatt']);
$fatt = ($_POST['oldFatt'] == 0 ? 1 : $_POST['oldFatt']);
$fatt = ($fatt == 0 ? 1 : $fatt);
$um = $_POST['umdesc'];
$close = ( isset($_POST['close']) ? $_POST['close'] : 0 );
$gruppo = (isset($_POST['gruppo']) ? $_POST['gruppo'] : "");
$nColli = (isset($_POST['ncolli']) ? $_POST['ncolli'] : 1);
$artCollo = (isset($_POST['artcollo']) ? $_POST['artcollo'] : "");
$desCollo = (isset($_POST['descollo']) ? $_POST['descollo'] : "");

//print($qta ." Ac".$newfatt ."B c".$fatt."\n");

// inizializziamo la pagina per tracciare eventuali problemi
head("Scrittura PL");

$qta = $qta * $newfatt / $fatt;

//print($qta ." A".$newfatt ."B ".$fatt);


$collopl = 0;
if($nColli < 2) {
    $collopl = $collo;
} else {
    //la riga va tutta nel collo con numero più alto.
    $collopl = $collo+1;
}

if( $prt < 2) {

	$Query = "SELECT quantita, quantitare, id_testa, codicearti, descrizion, unmisura, fatt, riffromr FROM docrig WHERE id = ".$id."";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die;

	print("Ricerca riga<br>");
	if($db->getField(riffromr) == 0) {
		print("<br>ATTENZIONE: riga PL manomessa.<br>Informare il CED.<br>");
		exit();
	}

	$qtaold = $db->getField(quantitare);
	if($db->getField(unmisura) != $um) {
		//devo eseguire la conversione di unita
		$qtaold = $qtaold * $db->getField(fatt) / $fatt;
	}

	if(trim($artCollo)=="") {

		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso, modify ) ";
		$Query .= "values ($id, $qta, $collopl, '".$lotto."', $fatt, '".$um."', '', '', 0, 0, 0, 0, 0, 0, 0 )";
		print("Inserimento riga suddivisione<br>");
		if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;

	} else {
		// ROBERTO 11.09.2013
		// nella packing list devo sdoppiare la riga in caso di doppio collo
		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso, modify ) ";
		$Query .= "values ($id, $qta, $collo, '$lotto', $fatt, '$um', '', '', 0, 0, 0, 0, 0, 0, 0 )";
		print("Inserimento riga suddivisione collo doppio<br>");
		if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;

		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, $qta, $collopl, '$lotto', $fatt, '$um', '$artCollo', '', 0, 0, 0, 0, 0, 0 )";
		print("Inserimento secondo collo<br>");
		if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;

		// add_scassi($db->getField(riffromr));

	}

	add_scassi($db->getField(riffromr));

	$Query = "update docrig set quantitare = ($qtaold-$qta), unmisura = '".$um."', fatt = $fatt where id = $id";
	print("Aggiornamento residuo PL<br>");
	if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;

	// Gestione del lotto sull'ordine (se necessario)
	if($lotto != "") {
		$rfr = $db->getField(riffromr);
		//MODIFICHIAMO SEMPRE RIGA ORDINE FINO AD OTTENERE QTA RES = 0
		// facciamo partire la gestione FOX
		$Query = "insert into u_plocmod (id, lotto, qta) values ($rfr, '".$lotto."', $qta)";
		print("Aggiornamento residuo ordine<br>");
		if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;
	}
}

//echo "close: $close, prt: $prt ".$_POST['print'].", collo: $collo \n";
if($close == 1 && $prt < 2) {
	//	echo "test.ok";w
	$Query = "select id_term from u_termpl where id_pl = $id_pl and collo = ".$collo."";
	if (!$db2->Execute($Query)){
		 print "<p> 02 - C'é un errore: " . $db2->errorMsg() . "<p>";
	}
	$tf = $db2->getField(id_term);
		$termFound = (integer)$tf;
	$Query = "update u_termpl set id_term = ($termFound + 1) where id_pl = ".$id_pl." and collo = ".$collo."";
	if (!$db2->Execute($Query)){
		 print "<p> 02 - C'é un errore: " . $db2->errorMsg() . "<p>";
	}

  header("location: pl_04_imb.php?id_pl=$id_pl&id=$id&collo=$collo&prt=$prt&ncolli=$nColli&artcollo=$artCollo&descollo=$desCollo");
	exit();
}

if( $prt > 0) {
  header("location: pl_06_selPrt.php?id=$id_pl&id_riga=$id&collo=$collo&ncolli=$nColli&artcollo=$artCollo&descollo=$desCollo");
} else {
	header("location: pl_01_ask.php");
}


function add_scassi($idOrd) {
	global $db2, $dbWrite;
	global $id, $qta, $collopl;

	$Query = "Select fogliomis from docrig where id=$idOrd";
	print("Lettura configurazione<br>");
	if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die;
	$cfType = simplexml_load_string($db2->getField(fogliomis), NULL, NULL, "cf", true);
	$type = $cfType->type;
	print("$type<br>");
	if ("EGO" == $type) {
		$cfData = simplexml_load_string($db2->getField(fogliomis));

		$cod = $cfData->serratura1->codice;
		if ("." != $cod) {
			$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
			$Query .= "values ($id, $qta, $collopl, '', 1, 'NR', '".$cod."', '', 0, 0, 0, 0, 0, 0 )";
			print("Inserimento scasso 1<br>");
			if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;
		}

		$cod = $cfData->serratura2->codice;
		if ("." != $cod) {
			$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
			$Query .= "values ($id, $qta, $collopl, '', 1, 'NR', '".$cod."', '', 0, 0, 0, 0, 0, 0 )";
			print("Inserimento scasso 2<br>");
			if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;
		}

		$cod = $cfData->serratura->codice;
		if ("." != $cod) {
			$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
			$Query .= "values ($id, $qta, $collopl, '', 1, 'NR', '".$cod."', '', 0, 0, 0, 0, 0, 0 )";
			print("Inserimento serratura<br>");
			if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>" and die;
		}

	}
}
?>
