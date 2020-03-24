<?php
include("header.php");
headx("Riepilogo inventario");

//Modalità di Accesso:
//1. A = per Articolo (normalmente chiamato da altra pagina)
//2. M = per Magazzino -> situazione Generale di Inventario
//3. U = per User -> tutte le sparate del terminale corrente
//4. "" = A scelta -> da Definire!
$mode = (isset($_GET['mode']) ? trim($_GET['mode']) : "");
$art = (isset($_GET['art']) ? trim($_GET['art']) : "");
$maga = (isset($_GET['maga']) ? trim($_GET['maga']) : "");
$lotto = (isset($_GET['lotto']) ? trim($_GET['lotto']) : "");

//Inizializzo Array Magazzini
if(isset($maga)){
	$dbMaga = getODBCSocket();
	$Query = "SELECT CODICE, DESCRIZION FROM MAGANA WHERE CODICE = '".$maga."' ORDER BY CODICE";
	if (!$dbMaga->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbMaga->errorMsg() . "<p>";
	if ($dbMaga->EOF){
		popupMsg("Magazzino Non Trovato", W);
	} else {
		$maga = $dbMaga->getField("Codice");
		$descMag = trim($dbMaga->getField("Descrizion"));
	}
	$dbMaga = null;
}

if(trim($mode) == 'A'){ //Ricerco Per Articolo
	$dbInv = getODBCSocket();
	$Query = "SELECT u_invent.*, magart.descrizion as DescArt, magart.unmisura ";
	$Query .= "FROM u_invent LEFT OUTER JOIN magart on magart.codice == u_invent.codicearti ";
	$Query .= "WHERE u_invent.codicearti = '".$art."' and u_invent.magazzino='".$maga."' and u_invent.lotto='".$lotto."'";
	if (!$dbInv->Execute($Query)) {
		popupMsg("01 : " . $dbInv->errorMsg()."", E) ;
	}
	if ($dbInv->EOF){
		$message = "Articolo non ancora Inventariato per il Magazzino: ".$maga." - ".$descMag."";
		popupMsg($message, W);
	} else {
		$art = trim($dbInv->getField("codicearti"));
		$descArt = trim($dbInv->getField("descart"));
		$unmisura = $dbInv->getField("unmisura");
		$lotto = trim($dbInv->getField("lotto"));
		$qta = $dbInv->getField("quantita");
		$id_mod = (($dbInv->getField(id_term) == $termid) ? "Questo Terminale" : "Altro Terminale");
		$data = date('j M Y g:i A', strtotime($dbInv->getField(timestamp)));
	}
	$dbInv = null;
	//INIZIO CODICE HTML
	print("<h3>Ultimo Articolo Inventariato</h3>");

	print ("<table border=\"1\">\n");

	print ("<tr>\n");
	print ("<th> Magazzino </th>\n");
	print ("<th> <span style=\"font-size: 9pt;\"> $maga </br> $descMag </span> </th>\n");
	print ("</tr>\n"); 

	print ("<tr>\n");
	print ("<th> Articolo </th>\n");
	print ("<th> $art</th>\n");
	print ("</tr>\n"); 

	print ("<tr>\n");
	print ("<th> Descrizione </th>\n");
	print ("<th> <span style=\"font-size: 9pt;\"> $descArt </span> </th>\n");
	print ("</tr>\n"); 

	print ("<tr>\n");
	print ("<th> Lotto </th>\n");
	print ("<th> $lotto</th>\n");
	print ("</tr>\n"); 
	
	print ("<tr>\n");
	print ("<th> Qta Inventariata </th>\n");
	print ("<th> $qta $unmisura</th>\n");
	print ("</tr>\n"); 

	print ("<tr>\n");
	print ("<th> Ultima Mod. di </th>\n");
	print ("<th> <span style=\"font-size: 9pt;\"> $id_mod </span> </th>\n");
	print ("</tr>\n"); 

	print ("<tr>\n");
	print ("<th> Data Ultima Mod. </th>\n");
	print ("<th> <span style=\"font-size: 9pt;\"> $data </span> </th>\n");
	print ("</tr>\n"); 

	print ("</table>");
	
	print("<br>\n<a class=\"menu\" style=\"font-size: 9pt;\" href=\"inventario.php?maga=$maga&art=$art&lotto=$lotto\"><img noborder src=\"b_search.gif\"/>Altra ricerca</a>\n");

}


footer();
?>

