<?php 
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");

$cCodice =  $_GET['codArt'];
$cCodice = str_replace("&amp;", "&", $cCodice); 
$cLotto = $_GET['codLotto'];
$maga = $_GET['maga'];
$anno =  $_GET['anno'];
if ($anno == ""){
	$anno = current_year();
}

$dbtest = getODBCSocket();

$return = "";
$unmisura = "";
$giac = "";

// cerchiamo giacenza totale Articolo 
if ($cLotto == ""){
	$Query = "Select maggiac.articolo, magart.unmisura, ";
	$Query .= "(maggiac.giacini+maggiac.progqtacar-maggiac.progqtasca+maggiac.progqtaret) as Giacenza ";
	$Query .= "from maggiac left outer join magart on magart.codice = maggiac.articolo ";
	$Query .= "where maggiac.articolo ='".$cCodice."' and maggiac.magazzino = '".$maga."' and maggiac.esercizio='".$anno."'";
	if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
	if ($dbtest->EOF) {
		$return = "*error*";
	} else {
		$return = (trim($cCodice));
		$unmisura = (trim($dbtest->getField(unmisura)));
		$giac = ($dbtest->getField(giacenza));
	}
} else {
	$Query = "Select maggiacl.lotto, magart.unmisura, ";
	$Query .= "(maggiacl.progqtacar-maggiacl.progqtasca+maggiacl.progqtaret) as Giacenza ";
	$Query .= "from maggiacl left outer join magart on magart.codice = maggiacl.articolo ";
	$Query .= "where maggiacl.magazzino = '".$maga."' and maggiacl.articolo = '".$cCodice."' ";
	$Query .= "and maggiacl.lotto = '".$cLotto."'";
	if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
	if ($dbtest->EOF) {
		$return = "*error*";
	} else {
		$return = (trim($cCodice));
		$unmisura = (trim($dbtest->getField(unmisura)));
		$giac = ($dbtest->getField(giacenza));
	}
}

$out = "<giacinfo>";
$out .= "<codice>". htmlspecialchars($return) ."</codice>";

if($return != "*error*") {
	$out .= "<unmisura>". $unmisura ."</unmisura>";
    $out .= "<giacenza>". $giac ."</giacenza>";
}

$out .= "</giacinfo>";

print($out); 


/*diconnect from database 
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;
*/ 

?>