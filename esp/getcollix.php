<?php 
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php"); 

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$id = substr(trim($_GET['id']),3,-1);

// cerchiamo id_testa della pl 
$Query = "select id_testa from docrig where id = $id";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
$id_testa = $db->getField(id_testa);

$out = "<colliinfo>";
$out .= "<id_testa>$id_testa</id_testa>";

// lista dei colli 
$Query = "select collo from u_termpl where id_pl = $id_testa";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
$out .= "<colli>"; 
while(!$db->EOF) {
	$out .= "<collo>";
	$out .= $db->getField(collo);
	$out .= "</collo>";
	$db->MoveNext();
} 
$out .= "</colli>"; 

$out .= "</colliinfo>";

print($out); 

?>