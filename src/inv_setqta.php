<?php
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php");
//include("odbcSocketLib.php");

$dbInv = getODBCSocket();

$articolo = $_GET['art'];
$qta = $_GET['qta'];
$lotto = $_GET['lotto'];
$id = $_GET['id'];
$fatt = $_GET['um'];
$ubicaz = $_GET['ubicaz'];
$maga = (isset($_GET['maga']) ? trim($_GET['maga']) : "");

/*$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);*/

/* cerchiamo se la riga c'e' gia' */
$Where = "where codcart = '$id'";
$Set = "id_term = $termid, timestamp = {^" . date("Y-m-d H:i:s") . "}";
$Query = "select * from u_invent $Where";
print($Query);
//$rs = $conn->Execute($Query);
if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}
if(!$dbInv->EOF) {
	$Query = "update u_invent set quantita = $qta*$fatt, $Set $Where";
	print($Query);
	//$rs = $conn->Execute($Query);
} else {
	if($maga == ""){
		$maga = "00" . substr($id, 4, 3);
	}
	$eserc = "20" . substr($id, 2, 2);
	$Query = "insert into u_invent (codicearti, quantita, magazzino, lotto, id_term, timestamp, codcart, esercizio, ubicaz) values (";
	$Query .= "'$articolo', $qta*$fatt, '$maga', '$lotto', $termid, {^" . date("Y-m-d H:i:s") . "}, '$id', '$eserc', '$ubicaz')";
	print($Query);
	//$rs1 = $conn->Execute($Query);
	if (!$dbInv->Execute($Query)) {
	    print($dbInv->errorMsg() . "$Query<br>");
	    return -1;
	}
}
/*diconnect from database
$conn->Close();
$rs = null;
$conn = null;
*/
header("location: inventario.php");
?>