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

// Cancellazione singola riga PL
$Query = "select id from docrig where id = $id";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
if(!$db->EOF)
{
    //la riga esiste nel database
    $Query = "delete from docrig where id = $id";
    if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
}


header("location: index.php");

?>