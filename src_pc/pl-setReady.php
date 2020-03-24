<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
// include("odbcSocketLib.php"); 
head("Pronto Merce");

$id_testa = $_GET['id_testa'];
$restore = $_GET['res'];

$db = getODBCSocket();

if ($restore == 0){
	$Query = "update doctes set numrighepr=0 where id=$id_testa and tipodoc='PL'";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}

	//$Query = "update docrig set quantitare=0 where id_testa=$id_testa and tipodoc='PL'";
	//if (!$db->Execute($Query)) {
	//	print($db->errorMsg() . "$Query<br>");
	//	return -1;
	//}
} else {
	$Query = "update doctes set numrighepr=2 where id=$id_testa and tipodoc='PL'";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}

	//$Query = "update docrig set quantitare=quantita where id_testa=$id_testa and tipodoc='PL'";
	//if (!$db->Execute($Query)) {
	//	print($db->errorMsg() . "$Query<br>");
	//	return -1;
	//}
}

$db = null;

print("<p style='font-weight:bold; text-align:center; padding:0;'>Reindirizzamento...</p>");

header("location:javascript://history.go(-1)");

?>