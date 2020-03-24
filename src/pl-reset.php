<?php 
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
//head("test");
$id = substr(trim($_GET['id']),3,-1);

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

// Nuova gestione concordata con Vincenzo 22.05.2012
// cancelliamo tutta la PL

$Query = "select id_testa from docrig where id = $id";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if(!$db->EOF) {
	$id_testa = $db->getField(id_testa);
	$Query = "delete from docrig where id_testa = $id_testa";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	$Query = "delete from doctes where id = $id_testa";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
}

// Vecchia gestione con ripristino della singola linea
// $Query = "select sum(quantita) as qta from docrig where val(cespite) = $id";
// $rs1 = $conn->Execute($Query) or die;
// if(!$rs1->EOF) {
	// $qtaold = $rs1->Fields[qta];
	// $Query = "update docrig set quantita = quantita + $qtaold where id = $id";
	// $rs = $conn->Execute($Query) or die;
	// $Query = "delete from docrig where val(cespite) = $id";
	// $rs = $conn->Execute($Query) or die;
// }

// $Query = "update docrig set u_costk = 1, lotto = \"\" where id = $id";
// $rs = $conn->Execute($Query) or die;


header("location: index.php");

?>