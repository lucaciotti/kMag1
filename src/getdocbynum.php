<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$tipodoc = trim($_GET['tipodoc']);
$num = trim($_GET['num']);
$anno = trim($_GET['anno']);


$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);

/* cerchiamo se esiste il documento nel picklist */

$Query = "Select id from doctes where tipodoc=\"$tipodoc\" and esercizio=\"$anno\" and numerodoc=";
$Query .= "\"" . sprintf("%6d", $num) . "  \"";
$rs = $conn->Execute($Query);
if ($rs->EOF)
{
    // Non abbiamo trovato nulla
    print("*error*");	
}
else
{
	print($rs->Fields[id]->Value);
}

//diconnect from database 
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;
 
?>