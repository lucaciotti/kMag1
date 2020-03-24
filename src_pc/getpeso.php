<?php
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("_pl_pesi.php");

$mode = $_GET['mode'];
$id_riga = $_GET['id_riga'];
$item = $_GET['item'];

// $conn = new COM("ADODB.Connection");
// $conn->Open($connectionstring);

$db = getODBCSocket();
/* cerchiamo l'ID della testa */
$Query = "Select id_testa from docrig where id =$id_riga";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
$id = $db->getField(id_testa);

if ( "collo" == $mode) {
    print( pesoCollo($id, $id_riga, $item) );
} else {
    print( pesoBanc($id, $id_riga, $item) );
}

//diconnect from database

?>
