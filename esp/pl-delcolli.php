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
$id = $_GET['id'];
$mode = $_GET['mode'];
$collo = $_GET['collo'];

$db = getODBCSocket();
$db2 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "insert into u_termlog (termreq, timestamp, id_pl, mode, collo) values (";
$Query .= "$termid, ";
$Query .= "{^" . date("Y-m-d H:i:s") . "}, ";
$Query .= "$id, '".$mode."', $collo)";
//print($Query);
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

$where0 = "where id_pl = $id";
if($mode == "R") {
	$where = $where0;
	if($collo != 0) {
		$where .= " and collo = $collo";
	}
	$Query = "select id_term, collo from u_termpl $where";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	while(!$db->EOF) {
		if($db->getField(id_term) % 2 == 1) {
			$Query = "update u_termpl set id_term = id_term -1 $where0 and collo = " . $db->getField(collo);
			if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
		}
		$db->MoveNext();
	}
} else {
	$where = $where0;
	if($collo != 0) {
		$where .= " and collo = $collo";
	}
	$Query = "delete from u_termpl $where";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	$where = "where id_testa = $id";
	if($collo != 0) {
		$where .= " and u_costk = $collo";
	}
	// 23.07.2013 Cancello anche gli imballi
	$Query = "delete from docrig $where and codicearti in (select codice from magart where danger)";
	if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
	$Query = "update docrig set u_costk=0, rifstato=\"\" $where";
	if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
}


header("location: pl-gestcolli.php");
?>