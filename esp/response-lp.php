<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php");

$id = substr($_GET['id'],3,-1);
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$anno = current_year();
$db = getODBCSocket();

$Query = "Select docrig.codicearti, docrig.descrizion, docrig.quantita, docrig.numerodoc, docrig.datadoc, docrig.tipodoc";
$Query .= ", docrig.codicecf, docrig.lotto, docrig.unmisura, anagrafe.descrizion as ragsoc, magart.pesounit,";
$Query .= " magart.pesounit*docrig.quantita as pesotot, magart.lotti";
$Query .= " from docrig inner join anagrafe on anagrafe.codice = docrig.codicecf";
$Query .= " inner join magart on magart.codice = docrig.codicearti";
$Query .= " where docrig.id = '".$id."'"; 
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

if ((!$db->EOF) && ($db->getField(tipodoc)=="LP")) {
	$desc = htmlentities($db->getField(descrizion));
	headx($db->getField(codicearti) . " - $desc");
	disableCR();
	print("<script type=\"text/javascript\" src=\"response-lp-utils.js\"></script>\n");
	print("<form name=\"lprow\" method=\"get\" action=\"write-lp.php\" onsubmit=\"return checkForm();\">\n");
	print("<table border=\"1\">\n");
	print("<tr>\n<th>" . $db->getField(codicearti) . "</th>\n<th>$desc</th>\n</tr>\n");
	print("<tr>\n<td>LP " . $db->getField(numerodoc) . "</td>\n<td>" . $db->getField(datadoc) . "</td>\n</tr>\n");
	print("<tr>\n<td>" . $db->getField(codicecf) . "</td>\n<td>" . htmlentities($db->getField(ragsoc)) . "</td>\n</tr>\n");
	print("<tr>\n<td>Quantit&agrave;</td>\n<td>");
    print("<input type=\"text\" name=\"qta\" id=\"qta\" value=\"" . $db->getField(quantita) . "\" onchange=\"calcolapeso(); return false;\"></td>\n</tr>\n");
	print("<tr>\n<td>Lotto</td>\n<td>");
	print("<input type=\"text\" name=\"lotto\" id=\"lotto\" value=\"" . $db->getField(lotto) . "\">");
	print("&nbsp;<span id=\"lottoobb\">" . ($db->getField(lotti) ? "Obbligatorio" : "") . "</span>");
	print("</td>\n</tr>\n");
    print("<tr>\n<td>U.M.</td>");
    print("<td>" . $db->getField(unmisura) . "</td>\n</tr>\n");
    print("<tr><td>Peso Unit.</td>");
    print("<td id='tdpesounit'>" . $db->getField(pesounit) . "</td>\n</tr>\n");
    print("<tr>\n<td>Peso Totale</td>");
    print("<td id='tdpesotot'> " . $db->getField(pesotot) . "</td>\n</tr>\n");
    print("<tr><td style=\"text-align: left\">\n");
	print("<input type=\"hidden\" name=\"id\" id=\"id\" value=\"$id\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\">\n");
	print("</td><td style=\"text-align: right\">\n");
	print("<input type=\"checkbox\" id=\"close\" name=\"close\" value=\"close\">Prelievo completato</td>\n</tr>\n");
	print("</table>\n");
	print("</form>\n");
	setFocus("lotto");

}
else if($db->getField(tipodoc)!="LP")
{
	head("Documento non corretto");
	print("<h2>Il Documento non corrisponde alle richieste (LP)</h2>");
}
else
{
	head("Non trovato");
	print("<h2>Riga articolo non trovata</h2>");
}

print("<br>\n<table style=\"border: none; width: 100%;\"><tr style=\"border: none;\">\n");
print("<td style=\"border: none;\"><a href=\"asklp.php\">Altra ricerca</a></td>\n");
print("</tr></table>\n");

footer();
?>