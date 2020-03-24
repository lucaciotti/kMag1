<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

//$num = str_pad(trim($_POST['num']), 6, " ", STR_PAD_LEFT) . "  ";
//$anno = trim($_POST['anno']);
$ncolli = $_POST['nbanc'];
$id_testa = $_POST['id_testa'];

// aggiorniamo le righe della packing list
for($j=1; $j <= $ncolli; $j++) {
//	$Query = "update docrig set u_traverso=" . $_POST["banc$j"];
//	$Query .= " where tipodoc=\"PL\" and esercizio=\"$anno\" and numerodoc=\"$num\" and u_costk1=$j";
//	$rs = $conn->Execute($Query);
	$codBanc = $_POST["codbanc$j"];
	$idRigaBanc = $_POST["idpal$j"];
	$misuras = $_POST["misuras$j"];
	$misural = $_POST["misural$j"];
	$altezza = $_POST["misurah$j"];
	$pesoBanc = $_POST["peso$j"];
	if(0 == $idRigaBanc) {		
		// inserisco il bancale
		$Query = "select id from docrig where id_testa = $id_testa and u_costk1 = $j";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
		$id = $db->getField(id);
		$Query = "insert into u_plmod (id, quantita, collo, lotto, fatt, unmisura, articolo, reparto, bancale, altezza, u_misural, u_misurah, u_misuras, peso ) ";
		$Query .= "values ($id, 1, 0, '', 1, '', '".$codBanc."', '', $j, $altezza, $misural, 0, $misuras, 0 )";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	} else {
		// aggiorno i dati esistenti
		$Query = "update docrig set codicearti='".$codBanc."', ";
		$Query .= "u_misural = $misural, U_misuras = $misuras, u_traverso = $altezza, prezzoacq = $pesoBanc ";
		$Query .= "where id = $idRigaBanc";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();
	}
}

header("location: askpl-banc.php"); 
?>