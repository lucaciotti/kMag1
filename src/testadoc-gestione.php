<?php
/************************************************************************/
/* Project ArcaWeb                               		    		    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head('');

/* devo fare il padding del numero come lo fa arca */
$id_testa = $_GET['id'];
$anno = current_year();

$db = getODBCSocket();
$db2 = getODBCSocket();
$db3 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$Query = "Select u_picklist.id, u_picklist.codicecf, u_picklist.datadoc, u_picklist.destdiv, u_picklist.note, ";
$Query .= " u_picklist.tipodoc, u_picklist.numerodoc, ";
$Query .= " anagrafe.descrizion as ragsoc, anagrafe.indirizzo, anagrafe.localita";
$Query .= " from u_picklist inner join anagrafe on anagrafe.codice = u_picklist.codicecf";
$Query .= " where u_picklist.id_testa = " . $id_testa ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

if ($db->EOF)
{
// non ci sono altre righe, mi comporto come se fosse stato cancellato l'intero documento
	head("Cancellato documento ".$id_testa);
	print("<script>");
	print('window.location = "gestione.php";');
	print("</script>");

	footer();
}

if( $db->getField(destdiv) == "    ")
{
  $destinatario = trim($db->getField(ragsoc));
  $indirizzo = trim($db->getField(indirizzo));
  $localita  = trim($db->getField(localita));
}
else
{
  $Query = "Select ragionesoc,indirizzo,localita from destinaz";
  $Query .= " where codicecf = \"" . $db->getField(codicecf) . "\"";
  $Query .= " and codicedes = \"" . $db->getField(destdiv) . "\"";
  if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();

  $destinatario = trim($db2->getField(ragionesoc));
  $indirizzo = trim($db2->getField(indirizzo));
  $localita  = trim($db2->getField(localita));

}
$tipodoc = $db->getField(tipodoc);
$numerodoc = $db->getField(numerodoc);
head($tipodoc . $numerodoc . trim($db->getField(ragsoc)) );
print('<table border="1" >');
print ("<tr><th>" . $tipodoc . $numerodoc . "</th><th>" . $db->getField(datadoc) . "</th></tr>");

print ("<tr><td>Cliente " . $db->getField(codicecf) ."</td><td>" . trim($db->getField(ragsoc)) . "</td></tr>");
print ("<tr><td>Destinazione </td><td>");
print ($destinatario . "<br/>");
print ($indirizzo . "<br/>");
print ($localita);
print ("</td></tr>");
print ("<tr><td>Note </td><td>&nbsp;" . str_replace(chr(13),"<br>",$db->getField(note)) . "</td></tr>");

print ('</table><BR/>');
print ('<table border="0" width="100%" ><tr>');


/* Righe documento */
$Query = "Select u_picklist.codicearti, u_picklist.descrizion, u_picklist.unmisura, u_picklist.quantitare, u_picklist.dataconseg, u_picklist.codicecf, ";
$Query .= " u_picklist.tipodoc, u_picklist.numerodoc, u_picklist.id, magart.ubicazione";
$Query .= " from u_picklist inner join magart on magart.codice = u_picklist.codicearti" ;
$Query .= " where u_picklist.id_testa = " . $id_testa;
$Query .= " order by magart.ubicazione, magart.codice";
if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();

print('<table border="1">');
print('<tr>');
print('<th>Cod. Art. Cli</th>');
print('<th>Codice</th>');
print('<th>Descrizione</th>');
print('<th>UM</th>');
print('<th>Residuo</th>');
print('<th>Giacenza</th>');
print('<th>Consegna</th>');
print('<th>Ubic.</th>');
print('<th>&nbsp;</th>');
print('</tr>');

while(!$db2->EOF) {

$Query = "Select giacini+progqtacar-progqtasca as giacenza from maggiac";
$Query .= " where magazzino='" . CONFIG::$DEFAULT_MAG . "' and esercizio='" . $anno . "' and articolo = '" . $db2->getField(codicearti) ."'"; 
if (!$db3->Execute($Query)) print "<p> 01 - C'é un errore: " . $db3->errorMsg() . "<p>" and die();
if (!$db3->EOF)
{
  $giacenza = $db3->getField(giacenza);
}
else
{
  $giacenza = "&nbsp;";
}

$Query = "Select codartfor from codalt ";
$Query .= " where codicearti =\"" . $db2->Fields[codicearti]->Value . "\"";
$Query .= " and codclifor =\"" . $db2->Fields[codicecf]->Value . "\"";
$db3 = $conn->Execute($Query);
if (!$db3->EOF)
{
  $codartfor = $db3->getField(codartfor);
}
else
{
  $codartfor = "&nbsp;";
}

print('<tr>');
print('<td>'.$codartfor.'</td>');
print('<td>'.$db2->getField(codicearti).'</td>');
print('<td>'. trim($db2->getField(descrizion)) .'</td>');
print('<td>'. $db2->getField(unmisura) .'</td>');
print('<td>'. $db2->getField(quantitare) .'</td>');
print('<td>'.$giacenza.'</td>');
print('<td>'. $db2->getField(dataconseg) .'</td>');
print('<td>'. $db2->getField(ubicazione) .'</td>');
print('<td><A HREF="rigadoc-cancella.php?id='. $db2->getField(id) .'&idtesta=' . $id_testa .'"><img noborder src="b_drop.png"/></a></td>');
print('</tr>');

$db2->MoveNext();
}
print('</table>');

/* Parte comune del piede di pagina */

print ('<table border="0" width="100%"><tr>');
print ('<td align="left"><A HREF="gestione.php"><img noborder src="b_search.png"/>Altra ricerca</A></td>');
print ('<td align="right"><A HREF="testadoc-cancella.php?id='. $id_testa .'"><img noborder src="b_deltbl.png"/>Cancella documento</A></td>');
print ('</tr></table>');

footer();
?>
