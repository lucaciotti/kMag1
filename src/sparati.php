<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Elenco righe acquisite");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select docrig.tipodoc, docrig.numerodoc, docrig.datadoc, docrig.codicecf, docrig.codicearti,";
$Query .= " docrig.quantita, docrig.quantitare, docrig.unmisura, docrig.descrizion,";
$Query .= " u_pick.quantita as arrivato,";
$Query .= " magart.ubicazione,";
$Query .= " anagrafe.descrizion as ragsoc, u_pick.id_riga as idpick " ;
$Query .= " from u_pick inner join docrig on u_pick.id_riga = docrig.id " ;
$Query .= " inner join anagrafe on anagrafe.codice = docrig.codicecf" ;
$Query .= " inner join magart on magart.codice = docrig.codicearti" ;
$Query .= " order by anagrafe.descrizion, docrig.datadoc, docrig.numerodoc, docrig.numeroriga" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

print("<table border=\"1\"><tr>\n");
print("<th>Cod.</th>\n");
print("<th>Ragione sociale</th>\n");
print("<th>Tipo</th>\n");
print("<th>Numero</th>\n");
print("<th>Data</th>\n");
print("<th>Cod. Art.</th>\n");
print("<th>Descrizione</th>\n");
print("<th>Ubic.</th>\n");
print("<th>Um</th>\n");
print("<th>Ordinato</th>\n");
print("<th>Residuo</th>\n");
print("<th>Acquisito</th>\n");
print("<th>ID Pick</th>\n");
print("<th>Elimina</th>\n");
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
  print("<td>" . $db->getField(ubicazione) . "</td>\n");
  print("<td>" . $db->getField(unmisura) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(quantita) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(quantitare) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(arrivato) . "</td>\n");
  print("<td align=\"right\">" . $db->getField(idpick) . "</td>\n");
    print("<td class=\"list\" style=\"text-align: center;\"><a href=\"delpick.php?id=" . $db->getField(idpick) . "\" >");
    print("<img src=\"../img/b_drop.png\" alt=\"elimina\" style=\"border: none;\">");
    print("</a></td>\n");
  print("</tr>\n");
  $db->MoveNext();
  }
print("</table>\n");
print("<br/>\n");
print("<a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a>\n");
footer();
?>