<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php");
include("odbcSocketLib.php");

$dbInv = getODBCSocket();
$dbInv1 = getODBCSocket();

$cCodice = trim($_GET['mag']);

/*$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);*/

$Query = "SELECT codice, descrizion, fiscale FROM MAGANA WHERE RIGHT(codice,3) = '$cCodice'";
//print($Query);
//$rs = $conn->Execute($Query);
if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

if ($dbInv->EOF) {
	print("{}");
} else {
	$maga = trim($dbInv->getField(codice));

	$obj = array(
		'maga' => $maga,
		'descrizion' => trim($dbInv->getField(descrizion)),
		'fiscale' => $dbInv->getField(fiscale)
	);

	print(json_encode($obj));
}

/*diconnect from database
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;
*/
?>