<?php 
/************************************************************************/
/* Project ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2008 by Roberto Ceccarelli                             */
/* */
/************************************************************************/

include("header.php");
$id = $_GET['id'];
$scatole = $_GET['scatole'];
$fasci = $_GET['fasci'];
$bancali = $_GET['bancali'];
$cassoni = $_GET['cassoni'];
$rotoli = $_GET['rotoli'];
$colli = $_GET['TOTALE'];
$aspbeni = $_GET['aspbeni'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "update doctes set ";
$Query .= " pqmt = $scatole,";
$Query .= " pqpasto = $fasci,";
$Query .= " pqrad = $bancali,";
$Query .= " pqsosta = $cassoni,";
$Query .= " pqtrasf = $rotoli,";
$Query .= " colli = \"$colli\",";
$Query .= " aspbeni = \"$aspbeni\" ";
$Query .= ' where id=' . $id;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

header("Location: controllo.php");
?>