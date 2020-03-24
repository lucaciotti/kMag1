<?php 
/************************************************************************/
/* Project ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2007 by Roberto Ceccarelli                             */
/*                                                                      */
/************************************************************************/

include("header.php");
$db = getODBCSocket();

$Query = "delete from u_picklist";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

head("Cancellata lista documenti");
print("<script>");
print('window.location = "gestione.php";');
print("</script>");

footer();
?>