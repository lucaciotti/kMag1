<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

// // $id = substr(trim($_GET['id']),3,-1);
// $conn = new COM("ADODB.Connection");
// $conn->Open($connectionstring);
$db = getODBCSocket();
$db2 = getODBCSocket();

$num = str_pad(trim($_GET['num']), 6, " ", STR_PAD_LEFT) . "  ";
$anno = trim($_GET['anno']);

$default_prt = "Packing_List_Grosse";

$libs = <<<EOT
<script type="text/javascript" src="../jquery-1.10.2.min.js"></script>
EOT;

$Query = "Select doctes.id, doctes.numerodoc, doctes.datadoc, doctes.codicecf, anagrafe.descrizion";
$Query .= " from doctes inner join anagrafe on anagrafe.codice = doctes.codicecf";
$Query .= " where doctes.tipodoc='PL' and doctes.esercizio='".$anno."' and doctes.numerodoc='".$num."'";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die;

if (!$db->EOF) {

	$id_testa = $db->getField(id);
	$descpl = "PL " . trim($db->getField(numerodoc)) . " " . trim($db->getField(descrizion)) ;
	baseHead($descpl, true, $libs);
	hiddenField("id_testa", $id_testa);
	print("<h3>Ristampa bancali<br>\n$descpl</h3>\n");
	print("<input type=\"checkbox\" onclick=\"toggleAll(this.checked)\" id=\"select_all\">\n");
	print("<label for=\"select_all\">Sel. tutti</label>\n<br><br>\n");
	print("<table>\n");

	$Query = "select docrig.u_costk1 as collo from docrig ";
	$Query .= " where id_testa=$id_testa and u_costk1 != 0 ";
	$Query .= " group by u_costk1 ";
	$Query .= " order by u_costk1 ";
	if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die;
	$maxcol = 5;
	$line = 0;
	$cnt = 0;
	while(!$db2->EOF) {
	$collo = $db2->getField(collo);
		if(0 == $cnt) {
			print("<tr>\n");
		}
		print("<td>\n<input type=\"checkbox\" class=\"linked collo $rep\" value=\"$collo\" id=\"$collo\">\n");
		print("<label for=\"$collo\">$collo</label>\n</td>\n");
		if($cnt == $maxcol-1) {
			print("</tr>\n");
			$cnt = 0;
			$line++;
		} else {
			$cnt++;
		}
		$db2->MoveNext();
	}
	closeTable($cnt, $line, $maxcol);

	print("<label for=\"prt\">Stampante</label>\n");
	print("<select name=\"prt\" id=\"prt\">\n");
	for($i = 0; $i < count($prtlist); $i++) {
		print("<option value=\"" . $prtlist[$i] . "\">");
		print($prtlist[$i]);
		print("</option>\n");
	}
	print("<option value=\"$default_prt\" selected=\"selected\">$default_prt</option>\n");
	print("</select>\n&nbsp;\n");
	print("<input type=\"button\" value=\"Stampa\" onclick=\"doPrintBanc();\">\n<br>\n");

	print("<br>\n");
    print("<a style=\"float: left;\" class=\"menu\" href=\"reprint-banc.php\">Altra ricerca</a><br>\n");

	print("<script type=\"text/javascript\" src=\"../reprint-pl-select.js\"></script>\n");
} else {
	head("Non trovato");
	print("<h2>Riga articolo non trovata</h2>");
	print ("<br><br>\n<a class=\"menu\" href=\"reprint-banc.php\">Altra ricerca</a>\n<br>\n");
}

goMain();
footer();

function closeTable($cnt, $line, $maxcol) {
	if($cnt > 0 && $line > 0) {
		for($j = $cnt; $j < $maxcol; $j++) {
			print("<td>&nbsp;</td>\n");
		}
		print("</tr>\n");
	}
	print("</table>\n<br>\n");
}

?>
