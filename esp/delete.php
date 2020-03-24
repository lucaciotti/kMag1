<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$table = $_GET['table'];
$id = $_GET['id'];

if( "all" == $id) { 
  $Query = "delete from $table";
}  
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

?>

