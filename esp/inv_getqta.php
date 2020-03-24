<?php
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

//OLD INVENTARIO...NON PIù UTILIZZATO

include("header.php");
 
$articolo = strtoupper($_GET['articolo']);
$maga = $_GET['maga'];
$lotto = (isset($_GET['lotto']) ? strtoupper($_GET['lotto']) : "");
$lotto = $lotto.trim();
$unmisura = strtoupper($_GET['unmisura']);
$anno = current_year();
$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);

$Query = "Select descrizion, ";
$Query .= "unmisura, unmisura1, unmisura2, unmisura3, fatt1, fatt2, fatt3 ";
$Query .= "from magart where codice = \"$articolo\"";
$rs1 = $conn->Execute($Query);
if (!$rs1->EOF)
{
//    setcookie("locked",$articolo,time()-1000);
	head($articolo . " - " . trim($rs1->Fields[descrizion]->Value));
	$um = $rs1->Fields[unmisura]->Value;
    print("<script type=\"text/javascript\" src=\"chknumber.js\"></script>\n");
	print("<form name=\"lettura\" method=\"get\" action=\"inv_setqta.php\">\n");
	print("<table border=\"1\">\n");
	
	print ("<tr>\n");
	print ("<th>$articolo<input type=\"hidden\" id=\"articolo\" name=\"articolo\" value=\"$articolo\"/></th>\n");
	print ("<th><span style=\"font-size: 9pt;\">" . trim($rs1->Fields[descrizion]->Value) . "</span></th>\n");
	print("</tr>\n");

	print ("<tr>\n");
	print ("<th>Lotto</th>\n");
	print ("<th>$lotto</th>\n");
	print ("</tr>\n");


	$Query = "Select quantita from u_invent where codicearti = \"$articolo\" and magazzino=\"$maga\" and lotto=\"$lotto\"";
	$rs = $conn->Execute($Query);
	$exist = "style=\"display: none;\"";
	$checked = "checked = \"\"";
	$giac = 0;
	if (!$rs->EOF)	{
		$giac = $rs->Fields[quantita]->Value;
		$exist = "";
		$checked = "checked = \"checked\"";
	}
	print ("<tr $exist>\n");
	print ("<th><label for=\"qtaold\">Q.ta inventariata</label></th>\n");
	print ("<th align=\"center\"><b>$giac &nbsp $um</b></th>\n");
	print ("</tr>\n");
	
	print ("<tr $exist>\n");
		print ("<th><label for=\"somma\">Somma Q.ta </br> (Deafult)</label></th>\n");
		print ("<td valign=\"center\"> &nbsp&nbsp&nbsp ");
				print ("<input type=\"checkbox\" id=\"somma\" name=\"somma\" $checked/> ");
			print ("<span style=\"color: red; font-size: 8pt;\">(<- Disabilita per reset Q.ta) </span>");
		print ("</td>\n");
    print ("</tr>\n");
	
	print ("<tr>\n<td><label for=\"qtanew\">Q.ta rilevata</label>\n");
	print ("</td>\n");
	print ("<td><input type=\"text\" id=\"qtanew\" name=\"qtanew\" size=\"10\">\n");
	print ("<select name=\"um\" id=\"um\">\n");
	print ("<option " . ($um == $unmisura ? "selected=\"selected\"" : "") . " value=\"1\">$um</option>\n");
	if($rs1->Fields[unmisura1]->Value != "  " && $rs1->Fields[unmisura1]->Value != $um && $rs1->Fields[fatt1]->Value != 0 ) {
		print("<option " . ($rs1->Fields[unmisura1]->Value == $unmisura ? "selected=\"selected\"" : "") . " value=\"" . $rs1->Fields[fatt1]->Value . "\">" . $rs1->Fields[unmisura1]->Value . "</option>\n");
	}
	if($rs1->Fields[unmisura2]->Value != "  " && $rs1->Fields[unmisura2]->Value != $um && $rs1->Fields[fatt2]->Value != 0) {
		print("<option " . ($rs1->Fields[unmisura2]->Value == $unmisura ? "selected=\"selected\"" : "") . " value=\"" . $rs1->Fields[fatt2]->Value . "\">" . $rs1->Fields[unmisura2]->Value . "</option>\n");
	}
	if($rs1->Fields[unmisura3]->Value != "  " && $rs1->Fields[unmisura3]->Value != $um && $rs1->Fields[fatt3]->Value != 0) {
		print("<option " . ($rs1->Fields[unmisura3]->Value == $unmisura ? "selected=\"selected\"" : "") . " value=\"" . $rs1->Fields[fatt3]->Value . "\">" . $rs1->Fields[unmisura3]->Value . "</option>\n");
	}
	print ("</select>\n");
	print ("</td>\n");
	
	print ("<tr>\n");
		print ("<td>");
			print ("<input type=\"hidden\" id=\"maga\" name=\"maga\" value=\"$maga\">");
			print ("<input type=\"hidden\" id=\"lotto\" name=\"lotto\" value=\"$lotto\">");
			print ("<input type=\"hidden\" id=\"unmisura\" name=\"unmisura\" value=\"$unmisura\">");
		print ("</td>\n");
		print ("<td> ");
			print ("<div id=\"ok1\"><input type=\"submit\" value=\"Carica\" onclick=\"return checkValue(document.getElementById('qtanew').value, 0);\"></div>");
			print ("<div id=\"mess\" style=\"display: none;\"></div>");
			print ("<div id=\"ok2\"  style=\"display: none;\"><input type=\"submit\" value=\"Carica\" onclick=\"return checkValue(document.getElementById('qtanew').value, 1);\"> <input type=\"submit\" value=\"Annulla\" onclick=\"return conferma(document.getElementById('qtanew').value, 0);\"></div>");
		print (" </td>\n");
	print ("</tr>\n");
 
	print ("</table>\n");
	print ("</form>\n");
	setFocus("qtanew");
}
else
{
//    setcookie("locked",$articolo,time()+8640000);
	head($articolo . " - Non trovato");
	print("<h2>Articolo $articolo non trovato</h2>");
}
//diconnect from database
$conn->Close();
$rs = null;
$rs1 = null;
$conn = null;

print("<br>\n<a href=\"inventario.php\"><img noborder src=\"b_search.gif\"/>Altra ricerca</a>\n");
footer();
?>