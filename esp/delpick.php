<?php
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

header("Location: sparati.php");
include("header.php");

$idpick = $_GET['id'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cancello l'id_riga nella tabella u_pick */

$Query = "delete from u_pick where id_riga = $idpick";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

// Cancello anche le righe nella u_picklist
$Query = "delete from u_picklist where id = $idpick";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
//disconnect from database

?>