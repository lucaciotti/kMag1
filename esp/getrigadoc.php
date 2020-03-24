<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = substr($_GET['cod'], 3, 9);
$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select codicearti,quantita from docrig where id =$id";

if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if (!$db->EOF) {
    print($db->getField(codicearti)."§".$db->getField(quantita));
} else {
  print('*error*');
}

 
?>