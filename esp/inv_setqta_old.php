<?php
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");


$articolo = $_GET['articolo'];
$qtanew = $_GET['qtanew'];
$somma = (isset($_GET['somma']) ? $_GET['somma'] : "off");
$fatt = $_GET['um'];
$maga = $_GET['maga'];
$lotto = $_GET['lotto'];

$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);

/* cerchiamo se la riga c'e' gia' */
$Where = "where codicearti = \"$articolo\" and magazzino=\"$maga\" and lotto=\"$lotto\"";
$Set = "id_term = $termid, timestamp = {^" . date("Y-m-d H:i:s") . "}";
$Query = "select * from u_invent $Where";
$rs = $conn->Execute($Query);
if(!$rs->EOF)
{
	if("on" == $somma)
	  $Query = "update u_invent set quantita = quantita+$qtanew*$fatt, $Set $Where";
	else
	  $Query = "update u_invent set quantita = $qtanew*$fatt, $Set $Where";
	$rs = $conn->Execute($Query);
}
else
{
	$Query = "insert into u_invent (codicearti, quantita, magazzino, lotto, id_term, timestamp) values (";
	$Query .= "\"$articolo\", $qtanew, \"$maga\", \"$lotto\", $termid, {^" . date("Y-m-d H:i:s") . "})";
	$rs = $conn->Execute($Query);
}
//diconnect from database
$conn->Close();
$rs = null;
$conn = null;

header("location: inventario.php?maga=$maga&art=$articolo&lotto=$lotto");
?>