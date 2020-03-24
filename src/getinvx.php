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

$dbInv = getODBCSocket();
$dbInv1 = getODBCSocket();

$cCodice = trim($_GET['cod']);


$Query = "SELECT * FROM U_INVENT WHERE Codcart = '$cCodice'";

if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

if ($dbInv->EOF) {
	$out = "<invinfo>";
	
	$out .= "<codicearti>*error*</codicearti>";
	
	$out .= "</invinfo>";

	print($out);
} else {
	$codart = trim($dbInv->getField(codicearti));

	$Query = "SELECT UNMISURA FROM MAGART WHERE CODICE = '$codart'";
	if (!$dbInv1->Execute($Query)) {
	    print($dbInv1->errorMsg() . "$Query<br>");
	    return -1;
	}

	$out = "<invinfo>";
	
	$out .= "<codicearti>".$codart."</codicearti>";
	if (trim($dbInv->getField(lotto))!=""){
		$out .= "<lotto>".trim($dbInv->getField(lotto))."</lotto>";
	} else {
		$out .= "<lotto>*none*</lotto>";
	}	
	$out .= "<unmisura>".trim($dbInv1->getField(unmisura))."</unmisura>";
	$out .= "<quantita>".$dbInv->getField(quantita)."</quantita>";
	if (trim($dbInv->getField(ubicaz))!=""){
		$out .= "<ubicaz>".trim($dbInv->getField(ubicaz))."</ubicaz>";
	} else {
		$out .= "<ubicaz>*none*</ubicaz>";
	}
	$out .= "<warn>".($dbInv->getField(warn) ? 1 : 0)."</warn>";
	
	$out .= "</invinfo>";

	print($out);
}

?>