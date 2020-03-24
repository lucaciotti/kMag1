<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Elenco righe acquisite");

$db = getODBCSocket();

$Query = "Select docrig.tipodoc, docrig.numerodoc, docrig.datadoc, docrig.codicecf, docrig.codicearti, docrig.quantita, docrig.quantitare, docrig.unmisura, docrig.descrizion,";
$Query .= " u_pick.quantita as arrivato, u_pick.id_riga,";
$Query .= " anagrafe.descrizion as ragsoc" ; 
$Query .= " from u_pick inner join docrig on u_pick.id_riga = docrig.id " ;
$Query .= " inner join anagrafe on anagrafe.codice = docrig.codicecf" ;
$Query .= " order by anagrafe.descrizion, docrig.datadoc, docrig.numerodoc, docrig.numeroriga" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

print("<table border=\"1\"><tr>\n");
print("<th>Cod.</th>\n");
print("<th>Ragione sociale</th>\n");
print("<th>Tipo</th>\n");
print("<th>Numero</th>\n");
print("<th>Data</th>\n");
print("<th>Cod. Art.</th>\n");
print("<th>Descrizione</th>\n");
print("<th>Um</th>\n");
print("<th>Ordinato</th>\n");
print("<th>Residuo</th>\n");
print("<th>Acquisito</th>\n");
print("<th>&nbsp;</th>\n");
print("</tr>\n");
while(!$db->EOF) {
  print("<tr>\n");
  print("<td>" . $db->getField(codicecf) . "</td>\n");
  print("<td>" . $db->getField(ragsoc) . "</td>\n");
  print("<td>" . $db->getField(tipodoc) . "</td>\n");
  print("<td>" . $db->getField(numerodoc) . "</td>\n");
  print("<td>" . $db->getField(datadoc) . "</td>\n");
  print("<td>" . $db->getField(codicearti) . "</td>\n");
  print("<td>" . $db->getField(descrizion) . "</td>\n");
  print("<td>" . $db->getField(unmisura) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(quantita) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(quantitare) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(arrivato) . "</td>\n");
  print("<td onclick=\"del('u_pick','" . $db->getField(id_riga) . "');\"><img noborder src=\"b_drop.png\"/></td>\n");
  print("</tr>\n");
  $db->MoveNext();
  }
print("</table>\n");
print("<br/>\n");
print("<table width=\"100%\"><tr>\n");
print("<td><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a></td>\n");
print("<td align=\"right\"><span onclick=\"del('u_pick','all');\"><img noborder src=\"b_drop.png\"/>&nbsp;Cancella tutta la lista</span></td>\n");
print("</tr></table>");
footer();
?>