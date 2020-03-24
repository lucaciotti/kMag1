<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Elenco ordini");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select max(u_picklist.tipodoc) as tipodoc, u_picklist.numerodoc, u_picklist.datadoc, max(u_picklist.codicecf) as codicecf, u_picklist.id_testa, max(u_picklist.user) as user,";
$Query .= " anagrafe.descrizion"; 
$Query .= " from u_picklist inner join anagrafe on anagrafe.codice = u_picklist.codicecf" ;
$Query .= " order by anagrafe.descrizion, u_picklist.datadoc, u_picklist.numerodoc, u_picklist.id_testa" ;
$Query .= " group by anagrafe.descrizion, u_picklist.datadoc, u_picklist.numerodoc, u_picklist.id_testa" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

print('<table border="1">');
print('<tr>');
print('<th>Ordine</th>');
print('<th>Data</th>');
print('<th>Cliente</th>');
print('<th>Prelievo</th>');
print('</tr>');
while(!$db->EOF) {
  print('<tr>');
  print('<td><a href="testadoc-gestione.php?id=' . $db->getField(id_testa) . '">');
  print( $db->getField(tipodoc) . $db->getField(numerodoc) . '</a></td>');
  print('<td>' . $db->getField(datadoc) . '</td>');
  print('<td>' . $db->getField(codicecf) . ' - ' . $db->getField(descrizion) . '</td>');
  print('<td><select id="user'. $db->getField(id_testa) .'" value="'. trim($db->getField(user)) . '"');
  print(' onchange="javascript:senduser('. $db->getField(id_testa) . ')">');
  utenti($db->getField(user));
  print('</select></td>');
  print('</tr>');
  $db->MoveNext();
  }
print('</table>');
print('<br/>');
print("<table width=\"100%\"><tr>\n");
print("<td><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a></td>\n");
print('<td align="right"><A HREF="cancella-tutto.php"><img noborder src="b_drop.png"/>&nbsp;Cancella tutta la lista</a></td>');
print("</tr></table>");
footer();
?>