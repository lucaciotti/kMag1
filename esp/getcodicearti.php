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

$dbtest = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

/* cerchiamo se esiste un articolo con il codice richiesto */

$Query = "Select codice from magart where codice ='" . $cCodice . "'";
if (!$dbtest->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbtest->errorMsg() . "<p>";
if ($dbtest->EOF)
{
  // Non abbiamo trovato l'articolo, vediamo se e' un alias
  $Query = "Select codicearti from magalias where alias = '" . $cCodice . "'";
  if (!$dbtest->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
  if ($dbtest->EOF)
  { // Non abbiamo trovato nemmeno l'alias
    print("*error*");	
  }
  else
  {
    print(trim($dbtest->getField(codicearti)));
  }
}
else
{
  print(trim($cCodice));
}

//diconnect from database 
//$rs->Close();
//$conn->Close();
//$rs = null;
//$conn = null;

?>