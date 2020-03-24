<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
//include("../models/PackingList.php");
include("odbcSocketLib.php");


head_jquery_pc("Evasione PL",$script);

$db = getODBCSocket();
$dbVettori = getODBCSocket();
$dbBO = getODBCSocket();
$dbOC = getODBCSocket();


$date = (isset($_GET['data']) ? $_GET['data'] : 0 );
if ($date==0){
	$data = date("m/d/Y", time());
	$dataStamp = date("d/M/Y", time());
} else {
	$data = date("m/d/Y", strtotime($date));
	$dataStamp = date("d/M/Y", strtotime($date));
}

$num = str_pad(trim($_GET['num']), 6, " ", STR_PAD_LEFT) . "  ";
$codVett = (isset($_GET['vett']) ? trim($_GET['vett']) : -1 );
$codNation = (isset($_GET['nat']) ? $_GET['nat'] : 0 ); //0 == ITALIA

print("<script type='text/javascript' src='../js/pl_pc_utils.js'></script>\n");

print("<h3 class='title'>Distinta Partenza Packing List</h3>");
print("<h3 class='title'>Prevista Evasione</br> il $dataStamp</h3>");

print("<div style='text-align:center;'>");
print("<p style='font-weight:bold;'>Data di Partenza:</p>");
print("<form action='pl-ready.php'>");
	print("<input type='date' name='data'>");
	print("<input type='submit' value='>'>");
	print("<input type='hidden' name='nat' id='nat' value='$codNation'>\n");
print("</form></div>");


print("<div id='navcontainer'>");
print("<p style='font-weight:bold; text-align:center; padding:0;'>Nazione:</p>");
print("<ul>");
if ($codNation==0) {
	print("<li><a class='selected' href='pl-ready.php?nat=0&data=$data'>ITALIA</a></li>");
	print("<li><a href='pl-ready.php?nat=1&data=$data'>ESTERO</a></li>");
} else {
	print("<li><a href='pl-ready.php?nat=0&data=$data'>ITALIA</a></li>");
	print("<li><a class='selected' href='pl-ready.php?nat=1&data=$data'>ESTERO</a></li>");
}

print("</ul></div>");


//CERCO I VETTORI
if($codNation==0){
	$Query = "SELECT DISTINCT doctes.vettore1 as codvettore, vettori.descrizion as vettore FROM doctes LEFT OUTER JOIN anagrafe on doctes.codicecf==anagrafe.codice LEFT OUTER JOIN vettori on doctes.vettore1==vettori.codice WHERE doctes.tipodoc=='PL' AND doctes.datadoc == CTOD('$data') AND anagrafe.codnazione=='I'";
} else {
	$Query = "SELECT DISTINCT doctes.vettore1 as codvettore, vettori.descrizion as vettore FROM doctes LEFT OUTER JOIN anagrafe on doctes.codicecf==anagrafe.codice LEFT OUTER JOIN vettori on doctes.vettore1==vettori.codice WHERE doctes.tipodoc=='PL' AND doctes.datadoc == CTOD('$data') AND anagrafe.codnazione<>'I'";
}
if (!$dbVettori->Execute($Query)) {
    print($dbVettori->errorMsg() . "$Query<br>");
    return -1;
}
if($codVett==''){
	$codVett = $dbVettori->getField(codvettore);
}
$dbVettori->moveFirst();
if($codVett == -1){
	$codVett = trim((string)$dbVettori->getField(codVettore));
}

print("<div id='navcontainer'>");
print("<p style='font-weight:bold; text-align:center; padding:0;'>Vettore:</p>");
print("<ul>");
while(!$dbVettori->EOF){
	$vett = trim((string)$dbVettori->getField(codVettore));
	if($codVett == $vett){
		print("<li><a class='selected' href='pl-ready.php?vett=".$vett."&nat=$codNation&data=$data''>".$dbVettori->getField(vettore)."</a></li>");
	} else {
		print("<li><a href='pl-ready.php?vett=".$vett."&nat=$codNation&data=$data''>".$dbVettori->getField(vettore)."</a></li>");
	}
	$dbVettori->moveNext();
}
print("</ul></div>");

//FILTRO TABELLA
print("<div style='text-align:center;'></br>");
print("Filtro: <input id='searchInput' value='Type To Filter'>");
print("</div></br>");

//INFORMAZIONI TESTA
if($codNation==0){
	$Query = "SELECT doctes.id, doctes.numerodoc, doctes.codicecf, doctes.numrighepr, doctes.nomodifica, vettori.descrizion as vettore, anagrafe.descrizion, nazioni.descrizion as Nazione FROM doctes LEFT OUTER JOIN anagrafe on doctes.codicecf==anagrafe.codice LEFT OUTER JOIN nazioni on nazioni.codice == anagrafe.codnazione LEFT OUTER JOIN vettori on doctes.vettore1==vettori.codice WHERE doctes.tipodoc=='PL' AND doctes.datadoc == CTOD('$data') AND anagrafe.codnazione=='I' AND doctes.vettore1=='$codVett' ORDER BY vettori.descrizion";
} else{
	$Query = "SELECT doctes.id, doctes.numerodoc, doctes.codicecf, doctes.numrighepr, doctes.nomodifica, vettori.descrizion as vettore, anagrafe.descrizion, nazioni.descrizion as Nazione FROM doctes LEFT OUTER JOIN anagrafe on doctes.codicecf==anagrafe.codice LEFT OUTER JOIN nazioni on nazioni.codice == anagrafe.codnazione LEFT OUTER JOIN vettori on doctes.vettore1==vettori.codice WHERE doctes.tipodoc=='PL' AND doctes.datadoc == CTOD('$data') AND anagrafe.codnazione<>'I' AND doctes.vettore1=='$codVett' ORDER BY vettori.descrizion";
	// doctes.u_note,
}
if (!$db->Execute($Query)) {
    print($db->errorMsg() . "$Query<br>");
    return -1;
}

print("<div><table id='maintable'>\n");
print("<thead>\n");
	print("<tr>");
	    print("<th style='width: 250px;'>Vettore</th>");
	    print("<th style='width: 60px;'>n. PL</th>");
	    print("<th style='width: 40px;'>Pronto Merce?</th>");
	    print("<th style='width: 150px;'>Cliente</th>");
	    print("<th style='width: 100px;'>Nazione</th>");
	    print("<th style='width: 100px;'>n. OC</th>");
	    print("<th style='width: 40px;'>n. BO</th>");
	    print("<th style='width: 60px;'>Totale Colli</th>");
	    print("<th style='width: 60px;'>Dettaglio Reparti</th>");
	    print("<th id='detail' style='width: 250px; ' rowspan='2'> Dettagli </th>");
	    //print("<th>Note</th>"); display: none;
    print("</tr>\n");
print("</thead>\n");

print("<tbody id='fbody'>\n");
  
while(!$db->EOF) {

	$idDoc=$db->getField(id);

	print("<tr>\n");

	print("<td style='text-align: center;'>".$db->getField(vettore)."</td>");
	print("<td style='text-align: center;'><a target='_blank' href='pl-edit.php?id_testa=".$db->getField(id)."'>".$db->getField(numerodoc)."</a></td>");
	//GESTIONE SEMAFORO
	if ($db->getField(nomodifica) && 1>2){
		print("<td style='text-align: center;'><img noborder src='../img/semaforoVerde.png' height=32></td>");
	} else {
		if ($db->getField(numrighepr)==0){
			print("<td style='text-align: center;'><button onclick='prontoMerce($idDoc, 1)'><img noborder src='../img/semaforoVerde.png' height=32></button></td>");
		} else {
			print("<td style='text-align: center;'><button onclick='prontoMerce($idDoc, 0)'><img noborder src='../img/semaforoRosso.png' height=32></button></td>");
		}
	}
	
	print("<td style='text-align: center;'>".$db->getField(codicecf)."<br/>".$db->getField(descrizion)."</td>");
	print("<td style='text-align: center;'>".$db->getField(nazione)."</td>");
	
	//CERCO I RIFERIMENTI AGLI OC RIGA PER RIGA
	$Query = "SELECT doctes.numerodoc, doctes.id from doctes where doctes.id IN (SELECT DISTINCT docrig.riffromt FROM docrig WHERE docrig.codicearti<>'' and docrig.tipodoc=='PL' AND docrig.id_testa==".$db->getField(id).")";
	if (!$dbOC->Execute($Query)) {
	    print($dbOC->errorMsg() . "$Query<br>");
	    return -1;
	}
	$entrato = false;
	$idOC=$dbOC->getField(id);
	while(!$dbOC->EOF) {
		if(!$entrato){
			$oc = $dbOC->getField(numerodoc);
			$entrato = true;
		} else {
			$oc .= " - " .$dbOC->getField(numerodoc);
		}
		$dbOC->MoveNext();
	}
	print("<td style='text-align: center;'>".$oc."</td>");

	//CERCO EVENTUALI BOLLE --> Richiede troppo tempo...già è lenta di suo
	if ($db->getField(nomodifica)){
		print("<td style='text-align: center;'><button onclick='findBO($idOC, $idDoc)'><img noborder src='../img/fi-refresh.png' height=24></button></td>");
	} else {
		print("<td style='text-align: center;'></td>");
	}

	//CERCO I PREPARATI PER REPARTO __ STOP RICERCA DETTAGLIATA -> Sarà Integrata in Pagina successiva!

	//-> IN COMPENSO CERCO IL TOTALE DEL PREPARATO  -- GESTIONE AJAX
	print("<td id='$idDoc.tot' style='text-align: center;'></td>");
	print("<script type=\"text/javascript\">totReparto($idDoc)</script>\n");
	
	print("<td style='text-align: center;'><button onclick='detailReparto($idDoc)'><img noborder src='../img/fi-results.png' height=24></button></td>");

	//print("<td style='text-align: center;'>".$db->getField(u_note)."</td>");

	print("<td id='$idDoc.detail' style='display: none;' rowspan='2'></td>");

	print("</tr>\n");
	$db->moveNext();
}
print("</tbody>\n</table>\n</div>");

print("<div style='text-align:center;'></br><p style='font-weight:bold; text-align:center; padding:0;'>Download File Excel</p>");
print("<button onclick='excelFile(\"".$data."\", $codNation)'><img noborder src='../img/fi-refresh.png' height=24> PL</button>");
print("<button onclick='excelFile2(\"".$data."\", $codNation)'><img noborder src='../img/fi-refresh.png' height=24> PB</button></div></br>");

print("<script type=\"text/javascript\" src=\"../tableFilter.js\"></script>\n");
	
logOut();
goMain();
footer();

function celleVuote($n){
	for($i=0; $i<$n; $i++){
		print("<td></td>");
	}
}

?>