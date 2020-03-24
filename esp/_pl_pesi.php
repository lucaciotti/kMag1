<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

//include("header.php");

function pesoCollo($id, $id_riga, $collo) {

	$dbPesi = getODBCSocket();

	$error = false;

	// ROBERTO 29.07.2015
	// dobbiamo trovare il riferimento alla prebolla
	$Query = "select id_testa from docrig where tipodoc='PB' and riffromt = $id";
	if (!$dbPesi->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbPesi->errorMsg() . "<p>";
	if(!$dbPesi->EOF){
		$id = $dbPesi->getField(id_testa);
	}


	if ($id_riga != 0 && $collo != 0){
		$Query = "Select docrig.codicearti, docrig.quantita, docrig.u_costk as collo";
		$Query .= ", docrig.unmisura, docrig.fatt, magart.pesounit ";
		$Query .= " from docrig left join magart on magart.codice = docrig.codicearti ";
		$Query .= " where docrig.id_testa = $id AND docrig.u_costk = $collo ";
		$Query .= " UNION Select iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo) as codicearti, u_plmod.quantita, u_plmod.collo";
		$Query .= ", u_plmod.unmisura, u_plmod.fatt, magart.pesounit ";
		$Query .= " from u_plmod left join docrig on docrig.id = u_plmod.id";
		$Query .= "  left join magart on magart.codice = iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo)  ";
		$Query .= " where u_plmod.id = $id_riga AND u_plmod.collo = $collo";
		if (!$dbPesi->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbPesi->errorMsg() . "<p>";
	} else {
		if ($id_riga == 0 && $collo == 0){
			$Query = "Select docrig.codicearti, docrig.quantita, docrig.u_costk as collo";
			$Query .= ", docrig.unmisura, docrig.fatt, magart.pesounit ";
			$Query .= " from docrig left join magart on magart.codice = docrig.codicearti ";
			$Query .= " where docrig.id_testa = $id AND docrig.u_costk != $collo ";
			if (!$dbPesi->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbPesi->errorMsg() . "<p>";
		} else {
	//		print("ERRORE CONTATTARE AMMINISTRATORE!");
			$error = true;
		}
	}

	if(!$error){
		while (!$dbPesi->EOF) {
			$fatt = $dbPesi->getField(fatt);
			$pesounit = $dbPesi->getField(pesounit);
			$qtat = $dbPesi->getField(quantita);
			$peso = $peso + ($fatt*$pesounit*$qtat);
			$dbPesi->MoveNext();
		}
		return $peso;
	} else {
		return -1;
	}

}

function pesoBanc($id, $id_riga, $banc) {
	$dbPesi = getODBCSocket();
	$error = false;

	if ($id_riga != 0 && $banc != 0){
		$Query = "Select docrig.codicearti, docrig.quantita, docrig.u_costk1 as bancale";
		$Query .= ", docrig.unmisura, docrig.fatt, magart.pesounit ";
		$Query .= " from docrig left join magart on magart.codice = docrig.codicearti ";
		$Query .= " where docrig.id_testa = $id AND docrig.u_costk1 = $banc ";
		$Query .= " UNION Select iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo) as codicearti, u_plmod.quantita, u_plmod.bancale";
		$Query .= ", u_plmod.unmisura, u_plmod.fatt, magart.pesounit ";
		$Query .= " from u_plmod left join docrig on docrig.id = u_plmod.id";
		$Query .= "  left join magart on magart.codice = iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo)  ";
		$Query .= " where u_plmod.id = $id_riga AND u_plmod.bancale = $banc";
		if (!$dbPesi->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbPesi->errorMsg() . "<p>";
	} else {
		if ($id_riga == 0 && $banc == 0){
			$Query = "Select docrig.codicearti, docrig.quantita, docrig.u_costk1 as bancale";
			$Query .= ", docrig.unmisura, docrig.fatt, magart.pesounit ";
			$Query .= " from docrig left join magart on magart.codice = docrig.codicearti ";
			$Query .= " where docrig.id_testa = $id AND docrig.u_costk1 != $banc ";
			if (!$dbPesi->Execute($Query)) print "<p> 01 - C'� un errore: " . $dbPesi->errorMsg() . "<p>";
		} else {
	//		print("ERRORE CONTATTARE AMMINISTRATORE!");
			$error = true;
		}
	}

	if(!$error){
		while (!$dbPesi->EOF) {
			$fatt = $dbPesi->getField(fatt);
			$pesounit = $dbPesi->getField(pesounit);
			$qtat = $dbPesi->getField(quantita);
			$peso = $peso + ($fatt*$pesounit*$qtat);
			$dbPesi->MoveNext();
		}
		return $peso;
	} else {
		return -1;
	}

}

?>
