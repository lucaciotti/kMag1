<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

/* devo fare il padding del numero come lo fa arca */ 
$id_testa = $_GET['id'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$Query = "delete from u_picklist where id_testa = " . $id_testa;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

head("Cancellato documento ".$id);
print("<script>");
print('window.location = "gestione.php";');
print("</script>");


footer();
?>