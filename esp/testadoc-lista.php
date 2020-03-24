<?php 
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                               	        */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

/* lettura parametri */ 
$id_testa = $_GET['id'];
$user = $_GET['user'];
$mode = $_GET['mode'];
$doc = isset($_GET['doc']) ? $_GET['doc'] : 0;

$db = getODBCSocket();
$db2 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$Query = "Select u_picklist.id, u_picklist.codicecf, u_picklist.datadoc, u_picklist.destdiv, u_picklist.note, ";
$Query .= " u_picklist.tipodoc, u_picklist.numerodoc, "; 
$Query .= " anagrafe.descrizion as ragsoc, anagrafe.indirizzo, anagrafe.localita";
$Query .= " from u_picklist inner join anagrafe on anagrafe.codice = u_picklist.codicecf"; 
//$Query .= " inner join docrig on docrig.id = u_picklist.id" ;
$Query .= " where u_picklist.id_testa = " . $id_testa ;
//$Query .= " order by docrig.numeroriga";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();


$tipodoc = $db->getField(tipodoc);
$numerodoc = $db->getField(numerodoc);

if( $db->getField(destdiv) == "    ")
{
  $destinatario = trim($db->getField(ragsoc));	
  $indirizzo = trim($db->getField(indirizzo));
  $localita  = trim($db->getField(localita));
}
else
{
  $Query = "Select ragionesoc,indirizzo,localita from destinaz";
  $Query .= " where codicecf = '" . $db->getField(codicecf) . "'";
  $Query .= " and codicedes = '" . $db->getField(destdiv) . "'";
  if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();

  $destinatario = trim($db2->getField(ragionesoc));	
  $indirizzo = trim($db2->getField(indirizzo));
  $localita  = trim($db2->getField(localita));

}

head($tipodoc . $numerodoc . trim($db->getField(ragsoc)) );
print("<table border=\"1\" >\n");
print ("<tr>\n<th>" . $tipodoc . $numerodoc . "</th>\n<th>" . $db->getField(datadoc) . "</th>\n</tr>\n"); 

print ("<tr>\n<td>Cliente " . $db->getField(codicecf) ."</td>\n<td>" . trim($db->getField(ragsoc)) . "</td>\n</tr>\n"); 
print ("<tr>\n<td>Destinazione </td>\n<td>");
print ($destinatario . "<br>");
print ($indirizzo . "<br>");
print ($localita);
print ("</td>\n</tr>\n"); 
print ("<tr>\n<td>Note </td>\n<td class=\"alert\">&nbsp;" . $db->getField(note) . "</td>\n</tr>\n"); 

$Query = "select docrig.id from docrig where docrig.id_testa = $id_testa order by docrig.numeroriga";
if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();

print ("</table>\n<br>\n");
print ("<table border=\"0\" width=\"100%\" >\n<tr>\n");
print ("<td align=\"left\">\n");
print ("<a href='righe-lista.php?id=" . $db2->getField(id) . "&idtesta=$id_testa&user=$user&mode=$mode" . substr($db->getField(codicecf),0,1) . "&doc=" . $doc . "'>\n");
print ("<img noborder src=\"b_props.gif\"/>\nPreleva righe\n</a>\n</td>\n");

/* Parte comune del piede di pagina */

print ("<td align=\"right\">\n");
if(stripos($mode,"b") === false) {
  print("<a href=\"lista.php?user=$user\">");
} else {
    if($doc){
        print("<a href='getfordoc.php'>");
    }
    else{
        print("<a href=\"getdoc.php\">");
    }
}
print("\n<img noborder src=\"b_search.gif\"/>\nAltra ricerca\n</a>\n</td>\n");
print ("</tr>\n</table>\n");

footer();
?>
