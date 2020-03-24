<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = substr(trim($_GET['id']),3,-1);
// $conn = new COM("ADODB.Connection");
// $conn->Open($connectionstring);
$db = getODBCSocket();
$db2 = getODBCSocket();
$anno = current_year();

$Query = "Select docrig.codicearti, docrig.descrizion, docrig.quantita, docrig.quantitare, docrig.numerodoc, docrig.datadoc, docrig.u_costk as collo";
$Query .= ", docrig.codicecf, docrig.unmisura, docrig.lotto, docrig.id_testa, docrig.gruppo, anagrafe.descrizion as ragsoc ";
$Query .= ", magart.u_ncolli, magart.u_artcollo, magart.u_descollo ";
$Query .= " from docrig inner join anagrafe on anagrafe.codice = docrig.codicecf";
$Query .= " left join magart on magart.codice = docrig.codicearti ";
$Query .= " where docrig.id = ".$id."";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

if (!$db->EOF) { //SE TROVO LA RIGA

	$articolo = $db->getField(codicearti);
	$desc = htmlentities(trim($db->getField(descrizion)));
	$qtaRes =  $db->getField(quantitare);
	$numerodoc =  $db->getField(numerodoc);
	$datadoc = $db->getField(datadoc);
  $collo = $db->getField(collo);
	$codcli = $db->getField(codicecf);
	$umDoc = $db->getField(unmisura);
	$lotto = $db->getField(lotto);
	$id_pl = $db->getField(id_testa);
	$gruppo = $db->getField(gruppo);
	$ragsociale = htmlentities(trim($db->getField(ragsoc)));
  $nColli = $db->getField(u_ncolli);
  $artCollo = $db->getField(u_artcollo);
  $desCollo = $db->getField(u_desCollo);

	$Query = "select unmisura, unmisura1, unmisura2, unmisura3, fatt1, fatt2, fatt3, lotti from magart where codice = '".$articolo."'";
	if (!$db2->Execute($Query)) print "<p> 03 - C'é un errore: " . $db2->errorMsg() . "<p>";
	$umP = $db2->getField(unmisura);
	$um1 = $db2->getField(unmisura1);
	$um2 = $db2->getField(unmisura2);
	$um3 = $db2->getField(unmisura3);
	$fatt1 = $db2->getField(fatt1);
	$fatt2 = $db2->getField(fatt2);
	$fatt3 = $db2->getField(fatt3);
	$lottoOb = $db2->getField(lotti);

	// Roberto 10.10.2014
	// gli scassi vanno sempre sul secondo collo (se esiste)


	headx(trim($articolo) . " - $desc");

	print("<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/bootstrap.min.css\">");
	print("<script type=\"text/javascript\" src=\"../js/cleanCode.js\"></script>");
	disableCR();

	$closed = ""; //VARIABILE COLLO non Chiuso! & CONTROLLO
	if(	$qtaRes > 0) {
		$collo = getNewCollo($db->getField(id_testa), $nColli);
		$disabled = "";
		$btn_disabled = "";
	} else {
		if ($artCollo != "" && !empty($nColli) && $nColli>1) {
			//VUOL DIRE CHE IL COLLO è DOPPIO
      $collo = $collo - 1;
    }
		$disabled = " readonly=\"readonly\"";
		$btn_disabled = " disabled=\"disabled\"";
		//Se non lo trovo nella tabella dei Terminali allora è pure CHIUSO!!!
		$Query = "select id_term from u_termpl where id_pl = ".$id_pl." and collo = ".$collo."";
		if (!$db2->Execute($Query)) print "<p> 02 - C'é un errore: " . $db2->errorMsg() . "<p>";
		if(!$db2->EOF) {
			if($db2->getField(id_term) != $termid) {
				$closed = " readonly=\"readonly\"";
			}
		}
	}

	//SE DOPPIO COLLO => CHIUDO COLLO DEFAULT!
    if(trim($artCollo) != ""){
        $chiudicollo = "checked='checked'";
    }
    else{
        $chiudicollo = "";
    }

	//INIZIO PAGINA!!
	// Stampo Riferimenti PL
	print("<div style=\"font-size: 11pt; font-weight:bold;\"><span style=\"font-size: 14pt;\">&nbsp;PL " . $numerodoc . "</span> del " . $datadoc . "</div>\n");
	print("<div style=\"font-size: 11pt; font-weight:bold;\">&nbsp;per " . $ragsociale . " [" . $codcli . "]</div>\n");

	//FORM
	print("<form name=\"plrow\" id=\"plrow\" method=\"post\" action=\"pl_03_write.php\" ");
		print("onsubmit=\"return checkForm($id_pl, document.getElementById('collo').value, document.getElementById('close').checked, $nColli);\">\n");
	//TABLE
	print("<table border=\"1\">\n");
		print("<tr>\n");
			print("<th>Art.</th>\n");
			print("<th id=\"codart\" style=\"font-size: 14pt;\">" . trim($articolo) . "</th>\n");
		print("</tr>\n");
		print("<tr>\n");
			print("<th>Descrizione</th>\n");
			print("<th><span style=\"font-size: 9pt;\">$desc</span></th>\n");
		print("</tr>\n");
		//SPARO BARCODE!
		print("<tr>\n");
			print("<th>Barcode Art.</th>\n");
			print("<td><input type=\"text\" id=\"controllo\" onblur=\"decodeArt('$articolo', this);\"></td>\n");
		print("</tr>\n");
		//CODICE LOTTO
		print("<tr>\n");
			print("<th>Cod. Lotto</th>\n");
			print("<td>\n");
				print("<input type=\"text\" name=\"lotto\" id=\"lotto\" value=\"" . $lotto . "\"$disabled");
					print(" onblur=\"checkLotto('$articolo', this);\">\n");
				print("&nbsp;<span id=\"lottoobb\">" . ($lottoOb ? "Obbligatorio" : "") . "</span>");
			print("</td>\n");
		print("</tr>\n");
		//QTA RESIDUA E UM
		print("<tr>\n");
			print("<th>Quantit&agrave Residua&nbsp;</th>");
			if($qtaRes > 0 ){
				print("<td><b>$qtaRes &nbsp $umDoc</b></td>\n");
			} else {
				print("<td><b>$qtaRes &nbsp $umDoc - TUTTO SPARATO</b></td>\n");
			}
		print("</tr>\n");

		//QUANTITA SPARATA
		print("<tr>\n");
			print("<th>Qt&agrave Sparata</th>");
			print("<td>\n");
				print("<input type=\"text\" name=\"qta\" id=\"qta\" size=\"6\" value=\"" . $qtaRes . "\"$disabled");
					print(" onchange=\"checkQta('$articolo', this);\">\n");
				print("<select name=\"um\" id=\"um\">\n"); // onchange=\"updQta(this);\"
					print("<option " . ($umP == $umDoc ? "selected=\"selected\"" : "") . "  value=\"1\">$umP</option>\n");
					if($um1 != "  " && $um1 != $umP) {
						print("<option " . ($um1 == $umDoc ? "selected=\"selected\"" : "") . " value=\"" . $fatt1 . "\">$um1</option>\n");
					}
					if($um2 != "  " && $um2 != $umP) {
						print("<option " . ($um2 == $umDoc ? "selected=\"selected\"" : "") . " value=\"" . $fatt2 . "\">$um2</option>\n");
					}
					if($um3 != "  " && $um3 != $umP) {
						print("<option " . ($um3 == $umDoc ? "selected=\"selected\"" : "") . " value=\"" . $fatt3 . "\">$um3</option>\n");
					}
				print("</select>\n</td>\n");
			print("</td>\n");
		print("</tr>\n");

		//NUMERO COLLO
		print("<tr>\n");
			print("<th>Collo N.</th>\n");
			print("<td>\n");
				print("<input type=\"text\" size=\"4\" name=\"collo\" id=\"collo\" value=\"$collo\" onkeyup=\"soloNumeri('collo')\" ");
					print("onblur=\"checkCollo($id_pl, this.value, '', $nColli);\"$disabled>");
				print("&nbsp;\n");
				print("<input type=\"checkbox\" name=\"close\" id=\"close\" value=\"1\"$btn_disabled $chiudicollo>\n<label for=\"close\">Chiudi collo</label>\n");
				if($disabled == "") {
					print("<input type=\"checkbox\" name=\"print\" id=\"print\" value=\"1\">\n<label for=\"print\">Stampa collo</label>\n");
				} else {
					print("<a style=\"float: right;\" class=\"menu\" href=\"reprint-pl-select.php?num=".trim($numerodoc)."&anno=$anno\">Ristampa colli</a>\n");
				}
			print("</td>\n");
		print("</tr>\n");
	print("</table>\n");

	//hidden
	print("<input type=\"hidden\" name=\"id\" id=\"id\" value=\"$id\">\n");                     //id riga pl
	print("<input type=\"hidden\" name=\"id_pl\" id=\"id_pl\" value=\"$id_pl\">\n");            //id testa pl
	print("<input type=\"hidden\" name=\"umdesc\" id=\"umdesc\" value=\"$umDoc\">\n");          //UM Sparata
	//print("<input type=\"hidden\" name=\"umold\" id=\"umold\" value=\"$umDoc\">\n");            //UM riga
	print("<input type=\"hidden\" name=\"qtaRes\" id=\"qtaRes\" value=\"$qtaRes\">\n");         //qta residua riga
	print("<input type=\"hidden\" name=\"oldFatt\" id=\"oldFatt\" value=\"0\">\n");             //fatt um riga
  print("<input type=\"hidden\" name=\"newFatt\" id=\"newFatt\" value=\"0\">\n");             //fatt conversione tra um riga e um sparata
  print("<input type=\"hidden\" name=\"gruppo\" id=\"gruppo\" value=\"$gruppo\"\n>");         //gruppo articolo
  print("<input type=\"hidden\" name=\"ncolli\" id=\"ncolli\" value=\"$nColli\"\n>");         //nColli (se doppiocollo =2)
  print("<input type=\"hidden\" name=\"artcollo\" id=\"artcollo\" value=\"$artCollo\"\n>");   //art 2 collo
  print("<input type=\"hidden\" name=\"descollo\" id=\"descollo\" value=\"$desCollo\"\n>");   //desc 2 collo

  print("<input style=\"float: right\" type=\"submit\" name=\"btnok\" id=\"btnok\" value=\"Procedi\" $btn_disabled>\n");

	print("</form>\n");

	setFocus("controllo");

//JAVASCRIPT CODE
	print("<script type=\"text/javascript\" src=\"../jquery-1.10.2.min.js\"></script>\n");
	print("<script type=\"text/javascript\" src=\"../bootstrap.min.js\"></script>\n");
	print("<script type=\"text/javascript\" src=\"../bootbox.min.js\"></script>\n");
	print("<script type=\"text/javascript\">bootbox.setDefaults({animate: false})</script>\n");
	print("<script type=\"text/javascript\" src=\"../response-pl-utils.js\"></script>\n");
//////////////////////////////////////////////////////////////////////////////////////////////
	print("<br>\n");
  print("<a style=\"float: left;\" class=\"menu\" href=\"pl_01_ask.php\">Altra ricerca</a>\n");
	print("<a style=\"float: right;\" class=\"menu\" href=\"pl_06_selPrt.php?id=" . $id_pl . "&collo=0\">Stampa etichette</a>\n");

	print("<div style=\"clear: both;\"></div>\n");
	if($nColli == 2)
    {
        //visualizzo i dati del pannello
        visualizzaDatiPannello($artCollo, $collo, $gruppo);
    }

} else {
	head("Non trovato");
	print("<h2>Riga articolo non trovata</h2>");
	print ("<br><br>\n<a class=\"menu\" href=\"pl_01_ask.php\">Altra ricerca</a>\n<br>\n");
}

goMain();
footer();

function getNewCollo($id_pl) {
	global $termid;
	$dbCollo = getODBCSocket();
	$Query = "select collo from u_termpl where id_term = $termid and id_pl = $id_pl";
	if (!$dbCollo->Execute($Query)) print "<p> 04 - C'é un errore: " . $dbCollo->errorMsg() . "<p>";
	if( !$dbCollo->EOF) {
		return $dbCollo->getField(collo);
	} else {
		$Query = "select max(collo) as lastcollo from u_termpl where id_pl = $id_pl group by id_pl";
		if (!$dbCollo->Execute($Query)) print "<p> 05 - C'é un errore: " . $dbCollo->errorMsg() . "<p>";
		if (!$dbCollo->EOF) {
			$tlast = $dbCollo->getField(lastcollo);
			$last = (integer)$tlast;
			$last++;
			return $last;
		} else {
			return 1;
		}
	}
}

function visualizzaDatiPannello($art, $collo, $gruppo){
    global $conn;
    $collosup = $collo + 1;
		$dbEgo = getODBCSocket();
    //cerco il pannello nella distinta dell'ego battente
    $Query = "select magart.descrizion from magart where magart.codice = '" . $art . "'";
    if (!$dbEgo->Execute($Query)) print "<p> 06 - C'é un errore: " . $dbEgo->errorMsg() . "<p>";

    if(!$dbEgo->EOF){
        $descr = $dbEgo->getField(descrizion);
	}
	else {
	$descr = '2nd Collo Generico';
	}
        print("<table>\n");
        print("<tr>\n");
        print("<td><h3>Dati Pannello</h3></td>");
        print("<td style=\"text-align: center;\">\n");
				print("<input type=\"checkbox\" id=\"chkColloPann\" onclick=\"chkColloPann_click();\">\n");
				print("<label for=\"chkColloPann\">Non generare collo aggiuntivo</label>\n");
				print("</td>");
        print("</tr>\n");
        print("<tr>\n<th>Codice</th>\n<td>$art</td>\n</tr>\n");
        print("<tr>\n<th>Descrizione</th>\n<td>$descr</td>\n</tr>\n");
        print("<tr>\n<th>Collo</td>\n<th><span id=\"colloPann\">$collosup</span></td>\n</tr>\n");
        print("<tr>\n<th>Gruppo</th>\n<td>$gruppo</td>\n</tr>\n");
        print("</table>\n");
}
?>
