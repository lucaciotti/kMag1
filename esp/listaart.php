<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
$reparto = $_GET['reparto'];
$user = $_GET['user'];

head("Elenco articoli");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select u_picklist.codicearti, max(u_picklist.descrizion) as descrizion, magart.ubicazione, min(u_picklist.id) as primo";
$Query .= " from u_picklist inner join magart on magart.codice = u_picklist.codicearti" ;
if ($reparto > "") {
  $Query .= " where reparto='".$reparto."'";
  }
$Query .= " order by magart.ubicazione, u_picklist.codicearti" ;
$Query .= " group by magart.ubicazione, u_picklist.codicearti" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

print("<table border=\"1\">\n");
print("<tr>\n");
print("<th>Articolo</th>\n");
print("<th>Descrizione</th>\n");
print("<th>Ubicazione</th>\n");
print("</tr>\n");
while(!$db->EOF) {
  print("<tr>\n");
  print("<td>\n<a href=\"righe-art.php?art=" . urlencode(trim($db->getField(codicearti))) . "&id=" . $db->getField(primo));
  print("&user=$user&reparto=$reparto\">\n");
  print( $db->getField(codicearti) . "</a>\n</td>\n");
  print("<td>" . $db->getField(descrizion) . "</td>\n");
  print("<td>" . $db->getField(ubicazione) . "</td>\n");
  print("</tr>\n");
  $db->MoveNext();
  }
print("</table>\n");

print ("<br/><a href=\"start_rep.php\"><img noborder src=\"b_search.gif\"/>Altro reparto</a>\n");
footer();
?>