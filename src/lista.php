<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
$user = $_GET['user'];

head("Elenco ordini");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select max(u_picklist.tipodoc) as tipodoc, u_picklist.numerodoc, u_picklist.datadoc, max(u_picklist.codicecf) as codicecf, u_picklist.id_testa,";
$Query .= " anagrafe.descrizion"; 
$Query .= " from u_picklist inner join anagrafe on anagrafe.codice = u_picklist.codicecf" ;
if ($user > "") {
  $Query .= " where user = '". $user ."'";
  }
$Query .= " order by anagrafe.descrizion, u_picklist.datadoc, u_picklist.numerodoc, u_picklist.id_testa" ;
$Query .= " group by anagrafe.descrizion, u_picklist.datadoc, u_picklist.numerodoc, u_picklist.id_testa" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";


print('<table border="1">');
print('<tr>');
print('<th>Ordine</th>');
print('<th>Data</th>');
print('<th>Cliente</th>');
print('</tr>');
while(!$db->EOF) {
  print('<tr>');
  print('<td><a href="testadoc-lista.php?id=' . $db->getField(id_testa) . "&user=$user&mode=\">");
  print( $db->getField(tipodoc) . $db->getField(numerodoc) . '</a></td>');
  print('<td>' . $db->getField(datadoc) . '</td>');
  print('<td>' . $db->getField(codicecf) . ' - ' . $db->getField(descrizion) . '</td>');
  print('</tr>');
  $db->MoveNext();
  }
print('</table>');

print ("<br/><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a>\n");
footer();
?>