<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$cCodice = $_GET['cod'];
$cCF = $_GET['cf'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cerchiamo se esiste il documento nel picklist */

$Query = "Select id_testa from u_picklist where id_testa = ".$cCodice."";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if ($db->EOF)
{
    // Non abbiamo trovato nulla
    print("*error*");	
}
else
{
	print(trim($cCodice));
}

 
?>