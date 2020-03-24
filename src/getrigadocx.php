<?php 
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");

$id = substr(trim($_GET['cod']), 3, 9);
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$dbtest = getODBCSocket();

$Query = "Select codicearti, quantita, quantitare, lotto, tipodoc, numerodoc, datadoc, esercizio, id_testa from docrig where id =".$id."";

if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbtest->errorMsg() . "</p>";
if (!$dbtest->EOF) {
    $return = trim($dbtest->getField(codicearti));
} else {
  $return = '*error*';
}

$out = "<rigadoc>";
$out .= "<codicearti>".htmlspecialchars($return)."</codicearti>";
if($return !=  '*error*') {
	$out .= "<quantita>" . $dbtest->getField(quantita) . "</quantita>";
	$out .= "<quantitare>" . $dbtest->getField(quantitare) . "</quantitare>";
	$out .= "<lotto>" . trim($dbtest->getField(lotto)) . "</lotto>";
	$out .= "<tipodoc>" . trim($dbtest->getField(tipodoc)) . "</tipodoc>";
	$out .= "<numerodoc>" . trim($dbtest->getField(numerodoc)) . "</numerodoc>";
	$out .= "<datadoc>" . $dbtest->getField(datadoc) . "</datadoc>";
	$out .= "<esercizio>" . $dbtest->getField(esercizio) . "</esercizio>";
	$out .= "<id_testa>" . $dbtest->getField(id_testa) . "</id_testa>";
}
$out .= "</rigadoc>";

print($out);

?>