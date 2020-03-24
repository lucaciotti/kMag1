<?php
/************************************************************************/
/*Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
// include("odbcSocketObj.php");
include_once($_SERVER['DOCUMENT_ROOT'] . '/kMag1/libs/apiObj.php');
include("../baseheader.php");

session_start();

$prtlist = array(		
		0 => "Spagna_PL_piccole_SATO",
		1 => "Spagna_4_PL"
		//	1 => "Kanna_2"
		//	2 => "5"
		//	3 => "4",
		//	4 => "5",
		//	5 => "6",
		//  6 => "IT_0_ZM400_110x73"
	);
$BANC_PRT = '';

//Mi permette di costruire quante pi� connessioni necessito
function getODBCSocket(){
	// $db = new ODBCSocketServer;
	// $db->_setHostName("172.16.2.102");
	// $db->_setPort(9628);
	// $db->_setConnectionString("Provider=vfpoledb.1;Data Source=d:\arca\Arca_Spagna\ditte\Spagna\Private.dbc;Collating Sequence=Machine");
	$db = new apiObj();
	return $db;
}
//popUp Errori, Warning o Msg generici
/* function popupMsg($msg, $type){
	if($type == "E"){

		echo "<script type='text/javascript'>alert('FATAL ERROR: $msg  Contattare Amministratore! $prevPage');  history.go(-1);</script>";
		// TODO log
		// INTERROMPO L?ESECUZIONE DELLA PAGINA
		//header("location: ".$_SERVER['HTTP_REFERER']); window.location.assign(".$prevPage.")
	} else {
		echo "<script type='text/javascript'>alert('WARNING $msg ');</script>";
	}
} */

function logOut(){
	if(isset($_SESSION['UserPL'])){
		print ("<br>\n<a class=\"menu\" href=\"login.php?logOut=yes\"><img noborder src=\"../img/b_drop.png\">LogOut</a>\n");
	}
}

function checkPermission(){
	if(!isset($_SESSION['UserPL'])){
		Header("Location: login.php?error=1");
	}
}
?>
