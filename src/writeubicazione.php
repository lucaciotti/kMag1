<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$codice = $_GET['codice'];
$ubicazione = trim($_GET['ubicazione']);
$id = $_GET['id'];

$db = getODBCSocket();

/* testiamo l'eventuale ubicazione principale */
$Query = "Select ubicazione from magart where codice = '" . $codice . "'";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
$oldubic = trim($db->getField(ubicazione));
head("Ubicazione di $codice");
// print("$oldubic\n");  

$Query = "Select * from ubicaz where codice = '" . $ubicazione . "'";
if (!$db->Execute($Query)) print "<p> 02 - C'é un errore: " . $db->errorMsg() . "<p>";
if ( $db->EOF ) {
	$Query = "insert into ubicaz (codice, descrizion, username, timestamp) values ('".$ubicazione."', '".$ubicazione."', ";
	$Query .= "'Web application', {^" . date("Y-m-d") . "})";
	if (!$db->Execute($Query)) print "<p> 03 - C'é un errore: " . $db->errorMsg() . "<p>";
} 

/*  Gestione pila ubicazioni, sembra che al momento non serva più
if ( $oldubic != "" and $oldubic != $ubicazione) { 
	// dobbiamo sostituire l'ubicazione principale, eventualmente archiviamo quella attuale 
	$Query = "Select ubicazione, idprog from u_ubicaz where codicearti = \"$codice\" order by idprog";
	$rs = $conn->Execute($Query);
	$ub_found = false;
	$free = 0;  
	while( !$rs->EOF ) {
		if (trim($rs->Fields[ubicazione]->Value) == $oldubic) {
			$ub_found = true;
		} 
		if ($rs->Fields[idprog]->Value == $free) {
			$free++;
		}  
    	$rs->MoveNext();
	} 
	print($free);
	if( !$ub_found && ($free <10)) {
		$Query = "insert into u_ubicaz (codicearti, idprog, ubicazione) values (\"$codice\", \"$free\", \"$oldubic\")";
		print("$Query\n");
		$rs = $conn->Execute($Query);
	} 
} 
*/

if("M" == $id) {
	/* Sostituiamo l'ubicazione principale */
	$Query = "update magart set ubicazione = '".$ubicazione."' where codice = '".$codice."'";
	// print("$Query\n");
	if (!$db->Execute($Query)) print "<p> 04 - C'é un errore: " . $db->errorMsg() . "<p>";
} else {
    /* Sostituiamo una ubicazione aggiuntiva */
	$Query = "select * from u_ubicaz where idprog='".$id."' and codicearti='".$codice."'";
	if (!$db->Execute($Query)) print "<p> 05 - C'é un errore: " . $db->errorMsg() . "<p>";
	if(!$db->EOF) {
		// Esiste già il record per questo id
		if( $ubicazione != "" ) {
			$Query = "update u_ubicaz set ubicazione = '".$ubicazione."' ";
			$Query .= "where codicearti = '".$codice."' and idprog='".$id."'";
			if (!$db->Execute($Query)) print "<p> 06 - C'é un errore: " . $db->errorMsg() . "<p>";
		} else {
			$Query = "delete from u_ubicaz ";
			$Query .= "where codicearti = '".$codice."' and idprog='".$id."' ";
			if (!$db->Execute($Query)) print "<p> 07 - C'é un errore: " . $db->errorMsg() . "<p>";
		}	
	} else {
		// Il record non esiste
		if( $ubicazione != "" ) {
			$Query = "insert into u_ubicaz (idprog, codicearti, ubicazione) ";
			$Query .= "values ('".$id."', '".$codice."', '".$ubicazione."')";
			if (!$db->Execute($Query)) print "<p> 08 - C'é un errore: " . $db->errorMsg() . "<p>";
		}
	}
}

/*diconnect from database 
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;*/
 
?>