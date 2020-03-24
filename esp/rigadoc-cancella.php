<?php 
/************************************************************************/
/* Project ArcaWeb                               		       			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2007 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

/* devo fare il padding del numero come lo fa arca */ 
$id_testa = $_GET['idtesta'];
$id = $_GET['id'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$Query = "delete from u_picklist where id = " . $id;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

head("Cancellata riga ".$id);
print("<script>");
print('window.location = "testadoc-gestione.php?id=' . $id_testa .'";');
print("</script>");


footer();
?>