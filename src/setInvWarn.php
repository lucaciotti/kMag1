<?php

header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header

include("header.php");
//include("odbcSocketLib.php");

$dbInv = getODBCSocket();

$id = trim($_GET['id']);
$ubi = trim($_GET['ubi']);

/*$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);*/

$Where = "where codcart = '$id'";

$Query = "SELECT * FROM U_INVENT $Where";
//print($Query);
//$rs = $conn->Execute($Query);
if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

if ($dbInv->EOF) {
	print("{}");
} else {

	$Query = "update u_invent set warn = 1, ubicaz = '$ubi' $Where";
	//print($Query);
	if (!$dbInv->Execute($Query)) {
	    print($dbInv->errorMsg() . "$Query<br>");
	    return -1;
	} else {
		print("OK");
	}

}

?>