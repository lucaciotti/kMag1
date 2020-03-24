<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$articolo = strtoupper($_GET['articolo']);
$qta = $_GET['qta'];
$mode = strtoupper($_GET['mode']);
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$anno = current_year();

$db=getODBCSocket();

$id_testa = 0;
if ($mode == "CP") {
	// Stiamo effettuando un carico di produzione
	if ( checkDistinta($articolo) ) {
		$lotto = $anno . "-";
		$lotto .= date("mdHi");
	    // Dobbiamo controllare se l'articolo gestisce il lotto
		$Query = "Select lotti from magart where codice = '".$articolo."'";
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		if($db->getField(lotti)) {
			mostraDistinta($articolo, $qta, "", $lotto);
		} else {
			head("Carico $articolo");
            print("Verranno caricati $qta di $articolo\n");
			print("<form name=\"db\" id=\"db\" method=\"POST\" action=\"creaoi.php\">\n");
			print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"0\">\n");
			print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$articolo\">\n");
			print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$lotto\">\n");
			print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$qta\">\n");
			print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"$idRiga\">\n");
			print("<input type=\"hidden\" name=\"mode\" id=\"mode\" value=\"$mode\">\n");
			print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" />\n");

			print("</form>\n");
		}
	} else {
		head($articolo . " - Non trovato");
		print("<h2>Articolo " . $articolo . " non ha distinta</h2>\n");
	}
} else {
    // Stiamo facendo un prelievo
	// controlliamo il caso in cui abbiamo letto un ordine
	if (substr($articolo,0,3) == "292")
	{
		$idRiga = substr($articolo,3,9);
		$Query = "Select codicearti, quantita, descrizion, lotto from docrig where id = '".$idRiga."'";
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		if (!$db->EOF)
		{
			$articolo = $db->getField(codicearti);
			$quantita = $db->getField(quantita);
			$descrizion = htmlentities($db->getField(descrizion));
			$lottopadre = $db->getField(lotto);
		}
		if ( checkDistinta($articolo) ) {
			mostraDistinta($articolo, $quantita, $descrizion, $lottopadre, $idRiga);
		} else {
			head($articolo . " - Non trovato");
			print("<h2>Articolo " . $articolo . " non ha distinta</h2>\n");
		}
	} else {
		$quantita = $qta;
		if ( checkDistinta($articolo) ) {
			mostraDistinta($articolo, $quantita);
		} else {
			head($articolo . " - Non trovato");
			print("<h2>Articolo " . $articolo . " non ha distinta</h2>\n");
		}
	}
}

if("CP"==$mode) {
	print ("<br/><a href=\"askcp.php\"><img noborder src=\"b_search.gif\"/>Annulla tutto</a>\n");
} else {
	print ("<br/><a href=\"askdb.php\"><img noborder src=\"b_search.gif\"/>Annulla tutto</a>\n");
}
footer();

function checkDistinta($articolo) {
	global $db;

	$Query = "Select codpadre from distbase where codpadre = '".$articolo."'";
	if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	if (!$db->EOF)
	{
		return true;
	} else {
		return false;
	}
}

function mostraDistinta($articolo, $quantita, $descrizion, $lottopadre, $idRiga) {
	global $anno, $db, $mode;

    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
	head("$articolo - $descrizion");
    print("<script type=\"text/javascript\" src=\"ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"dbedit.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"csspopup.js\"></script>\n");

	$nCompLen  = xEsplodi($articolo, date('Y-m-d'), $quantita, &$aComp, 0, 0);
	// iframe per dettagli riga
	print("<div id=\"dettagli\" style=\"display:none;\">\n");
	print("<input type=\"hidden\" name=\"currentid\" id=\"currentid\" value=\"0\">\n");
	print("<table class=\"detail\" border=\"1\">\n");
	print("<tr><td>Codice</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"16\" name=\"codicearti\" id=\"codicearti\" ></td></tr>\n");
	print("<tr><td>Descr.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"36\" name=\"descrizion\" id=\"descrizion\" ></td></tr>\n");
	print("<tr><td>Qta</td>\n");
	print("<td><input type=\"text\" class=\"detail\" size=\"16\" name=\"qtariga\" id=\"qtariga\" ></td></tr>\n");
	print("<tr><td>Lotto</td>\n");
	print("<td><input type=\"text\" class=\"detail\" size=\"16\" name=\"codicelotto\" id=\"codicelotto\" >");
	print("<input type=\"button\" value=\"Stampa\" class=\"detail\" name=\"printbutton\" id=\"printbutton\" onclick=\"stampaEtichetta(document.getElementById('currentid').value);\">");
	print("</td></tr>\n");
	print("<tr><td>Ubic.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"36\" name=\"ubicazione\" id=\"ubicazione\" ></td></tr>\n");
	print("<tr><td>Giac.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"16\" name=\"giacenza\" id=\"giacenza\" ></td></tr>\n");
	print("</table>\n");
	// campi per la navigazione
	print("<table class=\"navbar\" border=\"0\" width=\"100%\" ><tr>\n");
    print("<td class=\"navbar\" width=\"33%\" align=\"left\"><a href=\"#\" onclick=\"prevRow();\"><img noborder src=\"b_prevpage.gif\"/>Prec.</a></td>\n");
	print("<td class=\"navbar\" width=\"33%\" align=\"center\"><a href=\"#\" onclick=\"popup('dettagli',-1);\"><img noborder src=\"05_edit.gif\"/>Chiudi</a></td>\n");
    print("<td class=\"navbar\" width=\"33%\" align=\"right\"><a href=\"#\" onclick=\"nextRow();\">Succ.<img noborder src=\"b_nextpage.gif\"/></a></td>\n");
	print("</tr></table>\n");
	print("</div>\n");

	// scrittura tabella con i dati trovati
	print("<form name=\"db\" id=\"db\" method=\"POST\" action=\"creaoi.php\">\n");
	print("<table id=\"tbl\" border=\"1\">\n");
	print("<thead><tr><th>Codice</th><th>Qta</th><th>Lotto</th><th>Ubicazioni</th></tr></thead><tbody id=\"tblbody\">\n");
	$msg = "I seguenti articoli non presentano giacenza sufficiente:\\n";
	$lmsg = false;
	for($j = 1; $j <= $nCompLen; $j++) {
		print("<tr id=\"riga$j\">\n");
		print("<td><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" onclick=\"popup('dettagli',$j);\" value=\"" . $aComp[$j][codice] . "\"></td>\n");
		print("<td><input type=\"text\" size=\"3\" name=\"qta$j\" id=\"qta$j\" onblur=\"validateQta(this," . $aComp[$j][consumo] . ");\" value=\"" . $aComp[$j][consumo] . "\"></td>\n");

		// cerco tra i lotti se c'� qualcosa
		$Query = "Select progqtacar-progqtasca+progqtaret as giacenza, lotto from maggiacl ";
		$Query .= "where articolo = '" . $aComp[$j][codice]. "' ";
		$Query .= "and magazzino = '" . CONFIG::$DEFAULT_MAG . "' ";
		$Query .= "order by lotto asc ";
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		print("<td><input type=\"text\" size=\"12\" name=\"lotto$j\" id=\"lotto$j\" value=\"");
		if (!$db->EOF)	{
			print($db->getField(lotto));
		}
		print("\" onblur=\"validateLotto(this);\">");
		print("<input type=\"button\" value=\"St.\" name=\"printbutton$j\" id=\"printbutton$j\" onclick=\"stampaEtichetta($j);\">");
		print("</td>\n");


		// cerco l'ubicazione, la descrizione e la giacenza
		$Query = "Select magart.ubicazione, magart.descrizion, ";
		$Query .= "(maggiac.giacini + maggiac.progqtacar + maggiac.progqtaret - maggiac.progqtasca) as giacen ";
		$Query .= "from magart inner join maggiac on maggiac.articolo == magart.codice and maggiac.magazzino == '" . CONFIG::$DEFAULT_MAG . "' and maggiac.esercizio == '".$anno."' ";
		$Query .= "where magart.codice = '" . $aComp[$j][codice]. "' ";
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		print("<td><span id=\"desc$j\" style=\"display:none;\">" . htmlentities($db->getField(descrizion) . "</span>\n");
		print("<span id=\"giac$j\" style=\"display:none;\">" . $db->getField(giacen) . "</span>\n");
		print("<span id=\"ubic$j\">" . htmlentities($db->getField(ubicazione)) );
		// controllo giacenza
		if( $db->getField(giacen) < $aComp[$j][consumo]) {
			$lmsg = true;
			$msg .= $aComp[$j][codice] . "\\n";
		}
		// Adesso stampiamo tutte le ubicazioni
		$Query = "Select ubicazione from u_ubicaz where codicearti='" . $aComp[$j][codice]. "' ";
		if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
		while(!$db->EOF)
		{
			print ("&nbsp;" . htmlentities($db->getField(ubicazione)) );
			$db->MoveNext();
		}
		print("</span></td>\n");

		// chiusura riga
		print("</tr>\n");
	}
	print("</tbody></table>\n");
	print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$nCompLen\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$articolo\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$lottopadre\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$quantita\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"$idRiga\">\n");
	print("<input type=\"hidden\" name=\"mode\" id=\"mode\" value=\"$mode\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" />\n");

	print("</form>\n");

	// se necessario avvertiamo che non ci sono abbastanza componenti
	if( $lmsg ) {
		print("<script type=\"text/javascript\">alert(\"$msg\");</script>\n");
	}

	// cerchiamo di aprire subito la pagina dei dettagli
	print("<script type=\"text/javascript\">popup('dettagli',1);</script>\n");

} // fine funzione mostraDistinta

function xEsplodi($codPadre, $dValida, $nQta, $aComp, $nCompLen, $nLevel)
{
	global $db;

	$nLevel += 1;
	if($nlevel > 10) {
	   print("<h2>Troppi livelli - probabile ricorsione</h2>");
	}

	$Query = "Select codcomp, unmisura, quantita, tipoparte, datainival, datafinval ";
	$Query .= "from distbase where codpadre='".$codPadre."' order by numeroriga";
	if (!$db->Execute($Query)) print "<p> 01 - C'� un errore: " . $db->errorMsg() . "<p>";
	while(!$db->EOF)
	{
		$nConsumo = $db->getField(quantita) * $nQta;
		$today=strtotime(date("Y-m-d"));
		if( floatval($db->getField(datainival)) == 0 ) {
			 $inidate = strtotime("-1 day");
		} else {
			 $inidate = strtotime(str_replace("/","-",$db->getField(datainival)));
		}
		if( floatval($db->getField(datafinval)) == 0 ) {
			 $findate = strtotime("+1 day");
		} else {
			 $findate = strtotime(str_replace("/","-",$db->getField(datafinval)));
		}
	//	print("$findate - ");
		if ($findate >= $today and $inidate <= $today) {
			switch($db->getField(tipoparte)) {
				case "T":
					// fittizio: non faccio nulla
					break;
				case "F":
					// fantasma: scendo di un livello
					$nCompLen = xEsplodi($db->getField(codcomp), $dValida, $nConsumo, &$aComp, $nCompLen, $nLevel);
					break;
				case "N":
				case " ":
					$nCompLen += 1;
					$aComp[$nCompLen] = array("codice" => $db->getField(codcomp),
						"consumo" => $nConsumo,
						"um" => $db->getField(unmisura));
					// normale: archivio
				break;
			}
		}
		$db->MoveNext();
	}

	return $nCompLen;
}
?>
