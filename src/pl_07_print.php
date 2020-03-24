<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = $_GET['id'];
$collo = getNumeric('collo');
$banc = getNumeric('banc');
$artCollo = ( isset($_GET['artcollo']) ? $_GET['artcollo'] : "" );
$desCollo = ( isset($_GET['descollo']) ? $_GET['descollo'] : "" );
$prt = ( isset($_GET['prt']) ? $_GET['prt'] : "0" );
$prtBanc = getNumeric('prtbanc');
$warnpeso = getNumeric('warnpeso');
$ncolli = $_GET['ncolli'];

$dbPrint = getODBCSocket();

//print($collo." ".$id." ".$artCollo." ".$desCollo." ".$prt." ".$split." ".$ncolli);

// ROBERTO 29.07.2015
// dobbiamo trovare il riferimento alla prebolla
$Query = "select id_testa from docrig where tipodoc='PB' and riffromt = $id";
if (!$dbPrint->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbPrint->errorMsg() . "<p>" and die();
if(!$dbPrint->EOF){
	$id = $dbPrint->getField(id_testa);
}

if( $collo > 0 ) {
	if(trim($artCollo)==""){
		$Query = "insert into u_etichpl (id_doc, collo, printer, artcollo, descollo, extracollo, warnpeso) ";
			$Query .= "values ($id, $collo, '".$prt."', '', '', 0, $warnpeso)";
		if (!$dbPrint->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbPrint->errorMsg() . "<p>" and die();
	}
	else {
		$collosup = $collo + 1;
		$Query = "insert into u_etichpl (id_doc, collo, printer, artcollo, descollo, extracollo, warnpeso) ";
			$Query .= "values ($id, $collosup, '".$prt."', '', '', 0, $warnpeso)";
		print($Query);
		if (!$dbPrint->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbPrint->errorMsg() . "<p>" and die();

		$Query = "insert into u_etichpl (id_doc, collo, printer, artcollo, descollo, extracollo, warnpeso) ";
			$Query .= "values ($id, $collo, '".$prt."', '', '', 0, $warnpeso)";
		print($Query);
		// ROBERTO 11.09.2013 - la descrizione deve essere sempre la stessa!
		//		$Query = "insert into u_etichpl (id_doc, collo, printer, artcollo, descollo, extracollo) values ($id, $collosup, \"$prt\", \"\", \"\", 0)";
		if (!$dbPrint->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbPrint->errorMsg() . "<p>" and die();
	}
}

// Roberto 30.09.2014
// Stampa etichetta bancale
if( $prtBanc > 0 && $BANC_PRT!='') {
	$Query = "insert into u_etichpl (id_doc, collo, printer, artcollo, descollo, extracollo, warnpeso) ";
	$Query .= "values ($id, -$banc, '".$BANC_PRT."', '', '', 0, $warnpeso)";
	if (!$dbPrint->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbPrint->errorMsg() . "<p>" and die();
}

header("location: pl_01_ask.php");

?>
