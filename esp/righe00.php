<?php 
/************************************************************************/
/* Project ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2008 by Roberto Ceccarelli                             */
/* */
/************************************************************************/

include("header.php");
$id = $_GET['id'];

headx("AIR - Righe documento");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select codicearti, descrizion, quantita, numerodoc, datadoc";
$Query .= " from docrig" ;
$Query .= ' where codicearti > " " and id_testa=' . $id;
$Query .= " order by numeroriga" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

$recCount = 0;
while(!$db->EOF) {
  $recCount += 1;
  $db->MoveNext();
  }

print("<form action=\"javascript:checkGlobale($recCount);\" name=\"lettura\">");  
print("<label for=\"controlloglobale\">Lettura etichette</label>&nbsp;");
print("<input type=\"text\" id=\"controlloglobale\" size=\"20\" name=\"casella\" >");
print("</form>");

print('<table id="lista">');
print('<tr>');
print('<th>Codice</th>');
print('<th>Q.ta</th>');
print('<th>Descrizion</th>');
print('</tr>');
$n = 1;
$db->MoveFirst();
while(!$db->EOF) {
  print("<tr class=\"unchecked\" id=\"riga$n\">");
  print("<td><input type=\"hidden\" id=\"codice$n\" value=\"" . trim($db->getField(codicearti)) . "\">" . trim($db->getField(codicearti)) );
  print("<input type=\"hidden\" id=\"controllo$n\" size=\"10\"></td>");
  print('<td>' . $db->getField(quantita) . '</td>');
  print('<td>' . trim($db->getField(descrizion)) . '</td>');
  print('</tr>');
  $n += 1;
  $db->MoveNext();
  }
print('</table>');

$db->MoveFirst();
print("<p>Num " . $db->getField(numerodoc) . " del " . $db->getField(datadoc) . "</p>");
print("<a href=\"controllo.php\">Nuovo controllo</a>");
print("<br>");
print("<a href=\"colli.php?id=$id\">Inserimento colli</a>");

footer();
?>