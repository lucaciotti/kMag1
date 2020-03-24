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

function rigacolli($desc, $code) {
  global $db;
  print("<tr>\n<td>");
  print("<label for=\"$desc\">$desc</label>&nbsp;");
  print("</td>\n<td>");
  print("<input type=\"text\" id=\"$desc\" size=\"20\" name=\"$desc\" onchange=\"sommaColli()\" ");
  print("value=\"" . $db->getField($code) . "\">");
  print("</td>\n</tr>\n");
}

headx("AIR - Colli documento");

$db = getODBCSocket();
$db1 = getODBCSocket();

$Query = "Select tipodoc, numerodoc, datadoc,";
$Query .= " colli, pqmt, pqpasto, pqrad, pqsosta, pqtrasf";
$Query .= " from doctes" ;
$Query .= " where id='" . $id . "'";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

print("<p>" . $db->getField(tipodoc) . " num " . $db->getField(numerodoc) . " del " . $db->getField(datadoc) . "</p>");

print("<form action=\"scrivicolli.php\" method=\"get\" name=\"lettura\">\n");  
print("<input type=\"hidden\" id=\"id\" size=\"20\" name=\"id\" value=\"$id\">\n");
print("<table>\n");
rigacolli("scatole","pqmt");
rigacolli("fasci","pqpasto");
rigacolli("bancali","pqrad");
rigacolli("cassoni","pqsosta");
rigacolli("rotoli","pqtrasf");
rigacolli("TOTALE","colli");
print("</table>\n");

$Query = "Select codice, descrizion";
$Query .= " from aspbeni" ;
if (!$db1->Execute($Query)) print "<p> 01 - C'é un errore: " . $db1->errorMsg() . "<p>";

print("<br><label for=\"aspbeni\">Aspetto dei beni</label>&nbsp;");
print("<select id=\"aspbeni\" name=\"aspbeni\">\n");

$db1->MoveFirst();
while(!$db1->EOF) {
  print("<option value=\"" .$db1->getField(codice) . "\">");
  print($db1->getField(descrizion));
  print("</option>\n");  
  $db1->MoveNext();
}

print("</select>");

print("<br>");
print("<input type=\"submit\" value=\"Invia dati\">\n");
print("</form>\n");



footer();
?>