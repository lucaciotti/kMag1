<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("../models/PackingList.php");
include("odbcSocketLib.php"); 
head("test");

checkPermission();

$id_riga = $_GET['id'];
$type = $_GET['type'];

$db = getODBCSocket();
$oc = getODBCSocket();
$pb = new PackingList;
$pl = new PackingList;
$goOn=false;
$notify = 0;

$rowPB = $pb->findByIdRiga($id_riga, $db);
if($rowPB<0){
	print("<h3>ERRORE NELL'ELABORAZIONE.</h3>");
	print("<p>Errore n°:".$result."</p>");
	print("<p>".$pb->getErrorMsg()."</p>");
} else {
	$pb->moveFirst();
	$id_testa = $pb->getField('id_testa');
	$id_Riga_PL = $pb->getField('idRigaOrig');
	$id_Tes_PL = $pb->getField('idTesOrig');
	
	if($type == 'ROW'){
		$rowPL = $pl->findByIdRiga($id_Riga_PL, $db);		
		if($rowPL<0){
			print("<h3>ERRORE NELL'ELABORAZIONE.</h3>");
			print("<p>Errore n°:".$result."</p>");
			print("<p>".$pl->getErrorMsg()."</p>");
		} else {
			$pl->moveFirst();
			$id_Riga_OC = $pl->getField('idRigaOrig');
			$id_Tes_OC = $pl->getField('idTesOrig');
			$goOn = true;
		}
	}
}

if($goOn){
	$pb->moveFirst();
	$articolo = $pb->getField('codicearti');
	$lotto = $pb->getField('lotto');
	$qtaPB = $pb->getField('quantita');
	$um = $pb->getField('unmisura');
	$collo = $pb->getField('collo');
	$bancale = $pb->getField('bancale');
	
	//CANCELLO LA RIGA DELLA PB
	$Query = "insert into u_plocdel (id_riga, qta, segno) values ($id_riga., $qtaPB, '-')";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
		exit();
	}
	
	//RIPRISTINO QTA RES in PL
	$Query = "update docrig set quantitare=quantitare + $qtaPB where id=$id_Riga_PL";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
		exit();
	}
	$notify = 1;
	
	if(trim($lotto) != ""){
		//CANCELLO ANCHE COLLO E BANCALA TODO
		print("Lotto: " .$lotto . "<br>");
		
		// ci sono i lotti, quindi abbiamo lavorato anche l'ordine
		// cerchiamo la riga corrispondente nell'ordine
		$Query = "SELECT id, quantitare FROM docrig";
		$Query .= " WHERE id_testa = " . $id_Tes_OC ;
		$Query .= " and rifstato='X'";
		$Query .= " and codicearti='" . $articolo . "'";
		$Query .= " and lotto='" . $lotto . "'";
		$Query .= " and unmisura='" . $um . "'";
		$Query .= " and quantita = 0 and quantitare > 0 ";
		//print("$Query<br>");
		if (!$oc->Execute($Query)) {
			print($oc->errorMsg() . "$Query<br>");
			return -1;
		}
		if (!$oc->EOF) { 										// ho trovato la riga aggiunta		
			$Query = "insert into u_plocdel (id_riga, qta, segno) values (".$oc->getField('id').", $qtaPB, '-')";
			//print("$Query<br>");
			if (!$db->Execute($Query)) {
				print($db->errorMsg() . "$Query<br>");
				return -1;
				exit();
			}
			// Infine risistemiamo la riga originale
			$Query = "insert into u_plocdel (id_riga, qta, segno) values (".$id_Riga_OC.", $qtaPB, '+')";
			//print("$Query<br>");
			if (!$db->Execute($Query)) {
				print($db->errorMsg() . "$Query<br>");
				return -1;
				exit();
			}
			$notify = 1+2;			
		} else {
			print("ATTENZIONE!! CONTATTARE AMMINISTRATORE! Qualcosa è andato storto!<br>");
		}
	}
} else if ($type=="IMB") {
	$pb->moveFirst();
	$articolo = $pb->getField('codicearti');
	$lotto = $pb->getField('lotto');
	$qtaPB = $pb->getField('quantita');
	$um = $pb->getField('unmisura');
	$collo = $pb->getField('collo');
	$bancale = $pb->getField('bancale');
	
	//CANCELLO LA RIGA DELLA PB
	$Query = "insert into u_plocdel (id_riga, qta, segno) values ($id_riga, $qtaPB, '-')";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
		exit();
	}
	$notify = 4;
}

$db = null;
$oc = null;
$pl = null;
$pb = null;

//SCRIVO LOG in FILE dentro /bc/ita/log_Del_pl.php

//print($notify);
print("Tra 1 sec. sar&agrave; automaticamente reindirizzato...<br>\n");

header('Refresh: 2; URL=pl-edit.php?id_testa='.$id_Tes_PL.'&notify='.$notify.'');
//header("location: pl-edit.php?id_testa=$id_Tes_PL&notify=$notify");
?>