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
//include("odbcSocketLib.php");

$dbInv = getODBCSocket();

$cCodice = trim($_GET['mag']);

$Query = "SELECT codice, descrizion, fiscale FROM MAGANA WHERE RIGHT(alltrim(codice),3) = '$cCodice'";

if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

if ($dbInv->EOF) {
	$out = "<magainfo>";
	
	$out .= "<maga>*error*</maga>";
	
	$out .= "</magainfo>";

	print($out);
} else {

	$out = "<magainfo>";
	
	$out .= "<maga>".trim($dbInv->getField(codice))."</maga>";
	$out .= "<descrizion>".trim($dbInv->getField(descrizion))."</descrizion>";
	$out .= "<fiscale>".($dbInv->getField(fiscale) ? 1 : 0)."</fiscale>";
	
	$out .= "</magainfo>";

	print($out);
}

?>