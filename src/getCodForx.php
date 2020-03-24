<?php
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");

$cCodice = $_GET['cod'];
$cCF = $_GET['cf'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cerchiamo se esiste il fornitore esiste nell'elenco dei fornitori */

$Query = "Select * from anagrafe where codice = 'F" . $cCodice . "'";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if ($db->EOF)
{
    // Non abbiamo trovato nulla
    $Codice = "*error*";
}
else
{
    //Ho trovato il fornitore
	$Codice = trim($db->getField(codice));
    $RagSoc = str_replace('&', 'e', trim($db->getField(descrizion)));
    $Indirizzo = trim($db->getField(indirizzo));
    $Cap = trim($db->getField(cap));
    $Localita = trim($db->getField(localita));
    $Prov = trim($db->getField(prov));
}

$out = "<forinfo>";
$out .= "<codice>$Codice</codice>";
if($Codice != "*error*") {
    $out .= "<ragsoc>" . $RagSoc . "</ragsoc>";
    $out .= "<indirizzo>" . $Indirizzo . "</indirizzo>";
    $out .= "<cap>" . $Cap . "</cap>";
    $out .= "<localita>" . $Localita . "</localita>";
    $out .= "<prov>" . $Prov . "</prov>";
}
$out .= "</forinfo>";
print($out);

//disconnect from database

?>