<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id_riga = $_GET['id'];
$quantita = $_GET['quantita'];
$id_testa = $_GET['idtesta'];
$user = $_GET['user'];
$mode = $_GET['mode'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cerchiamo di capire se il record esiste gia' e in caso aggiorniamo */

$Query = "Select id_riga from u_pick where id_riga = $id_riga";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if ($db->EOF)
{
  $Query = "insert into u_pick (id_riga, quantita, iddoc) values ($id_riga, $quantita, ' ')";	
}
else
{
  $Query = "update u_pick set quantita = $quantita where id_riga = $id_riga";
}
//print($Query);
if (!$db->Execute($Query)) print "<p> 02 - C'é un errore: " . $db->errorMsg() . "<p>";
header("Location: righe-lista.php?id=$id_riga&idtesta=$id_testa&user=$user&mode=$mode");

 
?>