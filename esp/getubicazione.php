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

$dbtest = getODBCSocket();

/* cerchiamo se esiste una ubicazione con il codice richiesto */

$Query = "Select codice from ubicaz where codice = '".$cCodice."'";
if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if ($dbtest->EOF)
{
    print("*error*");	
}
else
{
  print(trim($cCodice));
}

/*diconnect from database 
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;*/
 
?>