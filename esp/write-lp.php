<?php 
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = $_GET['id'];
$qta = $_GET['qta'];
$lotto = trim($_GET['lotto']);
$close = $_GET['close'];

//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$db = getODBCSocket();

$Query = "update docrig set quantita = '".$qta."', quantitare = '".$qta."', lotto = '".$lotto."', rifstato = 'S' where id = '".$id."'";
//$rs = $conn->Execute($Query) or die;
if (!$db->Execute($Query)) $error="01 - ".$db->errorMsg() and die;

if("close" == $close) {
	$Query = "select id_testa from docrig where id = '".$id."'";
	if (!$db->Execute($Query)) $error="02 - ".$db->errorMsg() and die;
	$id_testa = $db->getField(id_testa);

	$Query = "select count(id) as nrighepr from docrig where id_testa = '".$id_testa."' and quantitare > 0";
	if (!$db->Execute($Query)) $error="03 - ".$db->errorMsg() and die;
	$nrighepr = $db->getField(nrighepr);
	
	$Query = "update doctes set numrighepr = '".$nrighepr."' where id = '".$id_testa."'" ;
	if (!$db->Execute($Query)) $error="04 - ".$db->errorMsg() and die;
}

if($error!=""){
	printf('<script>alert('.$error.');</script>');
}

header("location: asklp.php"); 

?>