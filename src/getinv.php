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
//include("odbcSocketLib.php");

$dbInv = getODBCSocket();
$dbInv1 = getODBCSocket();

$cCodice = trim($_GET['cod']);

/*$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);*/

$Query = "SELECT * FROM U_INVENT WHERE Codcart = '$cCodice'";
//print($Query);
//$rs = $conn->Execute($Query);
if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

if ($dbInv->EOF) {
	print("{}");
} else {
	$codart = trim($dbInv->getField(codicearti));

	$Query = "SELECT UNMISURA FROM MAGART WHERE CODICE = '$codart'";
	if (!$dbInv1->Execute($Query)) {
	    print($dbInv1->errorMsg() . "$Query<br>");
	    return -1;
	}

	$obj = array(
		'codicearti' => $codart,
		'lotto' => trim($dbInv->getField(lotto)),
		'unmisura' => trim($dbInv1->getField(unmisura)),
		'quantita' => $dbInv->getField(quantita),
		'ubicaz' => $dbInv->getField(ubicaz),
		'warn' => $dbInv->getField(warn)
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