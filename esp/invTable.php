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
//include("odbcSocketLib.php");

checkPermission();

head_jquery_pc("Riepilogo Inventario",$script);

$db = getODBCSocket();

//* $anno = "2016";
$anno = "2019";

$num = (isset($_GET['num']) ? trim($_GET['num']) : -1 );
$ubi = (isset($_GET['ubi']) ? trim($_GET['ubi']) : '' );
$codMaga = (isset($_GET['maga']) ? $_GET['maga'] : '00002' );
$codOrd = (isset($_GET['ord']) ? $_GET['ord'] : 0 );
//$codVett = (isset($_GET['vett']) ? trim($_GET['vett']) : -1 );

print("<script type='text/javascript' src='../js/inventario-utils.js'></script>\n");

print("<h3 class='title'>Riepilogo sparate inventariali</br>Magazzino: $codMaga</h3>");

if($num > 0){
	print("<div style=\"margin: 0 auto 0;\" id=\"avviso\">\n");
	print("<fieldset style=\"width: 50%; margin: 0 auto 0;\"><legend><h3> MODIFICHE APPORTATE </h3></legend>\n");
	print("<p>Cartellino <b>n. $num RIPRISTINATO!</b></br>MAGAZZINO: <b>$codMaga</b></br>UBICAZIONE: <b>$ubi</b></br></p></fieldset></br>\n");
	print("</div>\n");
}

//SELEZIONE MAGAZZINO
$Query = "SELECT distinct u_printinv.magazzino as codice, magana.descrizion FROM u_printinv left outer join magana on magana.codice==u_printinv.magazzino where u_printinv.esercizio=='$anno' and u_printinv.magazzino!='' order by u_printinv.magazzino";

if (!$db->Execute($Query)) {
    print($db->errorMsg() . "$Query<br>");
    return -1;
}
$db->moveFirst();
print("<div style='font-weight:bold; text-align:center;'>");
print("<select style='width: 280px; font-size: 78%' id='maga' name='maga' onchange='magaTable(this);'>\n");
print("<option value=\"\"> - Scegli Magazzino - </option>\n");
while(!$db->EOF) {
	print("<option value=\"" . trim($db->getField(codice)) . "\">" . trim($db->getField(codice)) .  " - ".trim($db->getField(descrizion)) . "</option>\n");
	$db->MoveNext();
}
print("</select></div>\n");

//SELEZIONO COME VOGLIO LA TABELLA
print("<div id='navcontainer'>");
print("<p style='font-weight:bold; text-align:center; padding:0;'>Ordina Per:</p>");
print("<ul>");
if ($codOrd==0) {
	print("<li><a class='selected' href='invTable.php?ord=0&maga=$codMaga'>Cartellino</a></li>");
	print("<li><a href='invTable.php?ord=1&maga=$codMaga'>Articolo</a></li>");
	print("<li><a href='invTable.php?ord=2&maga=$codMaga'>Tot.Articolo</a></li>");
	print("<li><a href='invTable.php?ord=3&maga=$codMaga'>Segnalate!</a></li>");
} else {
	if($codOrd==1){
		print("<li><a href='invTable.php?ord=0&maga=$codMaga'>Cartellino</a></li>");
		print("<li><a class='selected' href='invTable.php?ord=1&maga=$codMaga'>Articolo</a></li>");
		print("<li><a href='invTable.php?ord=2&maga=$codMaga'>Tot.Articolo</a></li>");
		print("<li><a href='invTable.php?ord=3&maga=$codMaga'>Segnalate!</a></li>");
	} else {
		if($codOrd==2){
			print("<li><a href='invTable.php?ord=0&maga=$codMaga'>Cartellino</a></li>");
			print("<li><a href='invTable.php?ord=1&maga=$codMaga'>Articolo</a></li>");
			print("<li><a class='selected' href='invTable.php?ord=2&maga=$codMaga'>Tot.Articolo</a></li>");
		print("<li><a href='invTable.php?ord=3&maga=$codMaga'>Segnalate!</a></li>");
		} else {
			print("<li><a href='invTable.php?ord=0&maga=$codMaga'>Cartellino</a></li>");
			print("<li><a href='invTable.php?ord=1&maga=$codMaga'>Articolo</a></li>");
			print("<li><a href='invTable.php?ord=2&maga=$codMaga'>Tot.Articolo</a></li>");
			print("<li><a class='selected' href='invTable.php?ord=3&maga=$codMaga'>Segnalate!</a></li>");
		}
	}
}
print("</ul></div>");


//FILTRO TABELLA
print("<div style='text-align:center;'></br>");
print("Filtro: <input id='searchInput' value='Type To Filter'>");
print("</div></br>");

//INFORMAZIONI TESTA
if ($codOrd==0) {
	$Query = "SELECT u_invent.*, magart.descrizion, magart.unmisura FROM u_invent left outer join magart on magart.codice==u_invent.codicearti where u_invent.esercizio=='$anno' and u_invent.magazzino=='$codMaga'";
} else {
	if($codOrd==1){
		$Query = "SELECT u_invent.*, magart.descrizion, magart.unmisura FROM u_invent left outer join magart on magart.codice==u_invent.codicearti where u_invent.esercizio=='$anno' and u_invent.magazzino=='$codMaga' order by u_invent.codicearti, u_invent.lotto ";
	} else {
		if($codOrd==2){
			$Query = "SELECT u_invent.codicearti, SUM(u_invent.quantita) as quantita, u_invent.lotto, MAX(u_invent.magazzino) as magazzino, MAX(u_invent.codcart) as codcart, MAX(magart.descrizion) as descrizion, MAX(magart.unmisura) as unmisura FROM u_invent left outer join magart on magart.codice==u_invent.codicearti where u_invent.esercizio=='$anno' and u_invent.magazzino=='$codMaga' group by u_invent.codicearti, u_invent.lotto ";
		} else {
			$Query = "SELECT u_invent.*, magart.descrizion, magart.unmisura FROM u_invent left outer join magart on magart.codice==u_invent.codicearti where u_invent.esercizio=='$anno' and u_invent.magazzino=='$codMaga' and u_invent.warn ";
		}
	}
}

if (!$db->Execute($Query)) {
    print($db->errorMsg() . "$Query<br>");
    return -1;
}
$db->moveFirst();

print("<div><table id='maintable'>\n");
print("<thead>\n");
	print("<tr>");
	    print("<th style='width: 40px;'>n. Cartellino</th>");
	    print("<th style='width: 100px;'>Cod. Art.</th>");
	    print("<th style='width: 250px;'>Descr. Articolo</th>");
	    print("<th style='width: 60px;'>Lotto</th>");
	    print("<th style='width: 40px;'>U.M.</th>");
	    print("<th style='width: 40px;'>Qta Invent.</th>");
	    print("<th style='width: 100px;'>Ubicazione</th>");
	    print("<th style='width: 100px;'>Segnalata</th>");
	    print("<th style='width: 60px;'>Cancella Sparata</th>");
	    //print("<th id='detail' style='width: 250px; ' rowspan='2'> Dettagli </th>");
	    //print("<th>Note</th>"); display: none;
    print("</tr>\n");
print("</thead>\n");

print("<tbody id='fbody'>\n");

while(!$db->EOF) {

	$idCart=$db->getField(codcart);

	$maga="00".substr($idCart,4,3);
	$numCart=intval(substr($idCart,7,5));

	$codArt=$db->getField(codicearti);
	$descArt=trim($db->getField(descrizion));
	$unMisura=$db->getField(unmisura);
	$qtaInv=$db->getField(quantita);
	$lotto=$db->getField(lotto);
	$ubicazione=trim($db->getField(ubicaz));
	$warn=$db->getField(warn);

	print("<tr>\n");

	if($maga!=$db->getField(magazzino)){

	}

	print("<td style='text-align: center;'>".$numCart."</td>");
	print("<td style='text-align: center;'>".$codArt."</td>");
	print("<td style='text-align: center;'>".$descArt."</td>");
	print("<td style='text-align: center;'>".$lotto."</td>");
	print("<td style='text-align: center;'>".$unMisura."</td>");
	print("<td style='text-align: center;'>".$qtaInv."</td>");
	print("<td id='ubi' style='text-align: center;'>".$ubicazione."</td>");
	//GESTIONE SEMAFORO
	if ($warn){
		print("<td style='text-align: center;'><img noborder src='../img/redFlag.gif' height=28></td>");
		//print("<td style='text-align: center;'><a href='invDelCart.php?id=".$idCart."&ubi=".$ubicazione."'><img noborder src='../img/delete.png' height=28></a></td>\n");
		print("<td style='text-align: center;'><button onclick='deleteCart($idCart);'><img noborder src='../img/delete.png' height=28></button></td>\n");
	} else {
		print("<td style='text-align: center;'></td>");
		print("<td style='text-align: center;'></td>");
	}

	//print("<td><a href='pl-delrow.php?id=".$pl->getField(id_riga)."&id_testa=".$pl->getField(id_testa)."&idOrig=".$pl->getField(idRigaOrig)."'><img noborder src='../img/delete.png' height=32></a></td>\n");

	print("<td id='$idDoc.detail' style='display: none;' rowspan='2'></td>");

	print("</tr>\n");
	$db->moveNext();
}

print("</tbody>\n</table>\n</div>");

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
