<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$codicearti = $_GET['codicearti'];
$lotto = $_GET['lotto'];
$reparto = $_GET['reparto'];
$quantita = $_GET['quantita'];
if($quantita <1) {
	$quantita = 1;
}
$prt = ( isset($_GET['prt']) ? $_GET['prt'] : "" );

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cerchiamo di capire se il record esiste gia' e in caso aggiorniamo */

$Query = "insert into u_etich (codicearti, lotto, reparto, printer) values ('".$codicearti."', '".$lotto."', '".$reparto."', '".$prt."')";
for($j =0; $j < $quantita; $j++) {
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
}

 
?>