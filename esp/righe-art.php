<?php
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = $_GET['id'];
$articolo = $_GET['art'];
$user = $_GET['user'];
$reparto = $_GET['reparto'];
$anno = current_year();

$db = getODBCSocket();
$db2 = getODBCSocket();
$db3 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select u_picklist.codicearti, u_picklist.descrizion, u_picklist.unmisura, u_picklist.quantitare, u_picklist.dataconseg, u_picklist.codicecf, ";
$Query .= " u_picklist.tipodoc, u_picklist.numerodoc, u_picklist.id, u_picklist.reparto, magart.ubicazione,";
$Query .= " docrig.quantitare as quantitarenew, anagrafe.descrizion as ragionesoc, docrig.codicecf, ";
$Query .= " u_pick.quantita as picked";
$Query .= " from u_picklist inner join magart on magart.codice = u_picklist.codicearti" ;
$Query .= " inner join docrig on docrig.id = u_picklist.id" ;
$Query .= " inner join anagrafe on docrig.codicecf = anagrafe.codice" ;
$Query .= " left outer join u_pick on u_pick.id_riga = u_picklist.id ";
$Query .= " where u_picklist.codicearti = '".$articolo."'";
$Query .= " order by u_picklist.id";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();


// ho cercato tutte le righe di ordine perche' mi serviranno per conoscere precedente e successiva
//adesso devo fare lo scan per posizionarmi sulla riga richiesta
$prev = 0;
while($db->getField(id) != $id) {
  $prev = $db->getField(id);
  $db->MoveNext();
}

// Lettura giacenze
$Query = "Select giacini+progqtacar-progqtasca as giacenza from maggiac";
$Query .= " where magazzino='00002' and esercizio='". $anno ."' and articolo = '" . $db->getField(codicearti) ."'"; 
if (!$db3->Execute($Query)) print "<p> 01 - C'é un errore: " . $db3->errorMsg() . "<p>" and die();
if (!$db3->EOF)
{
  $giacenza = $db3->getField(giacenza);
}
else
{
  $giacenza = "&nbsp;";
}


// Lettura codici alternativi
$Query = "Select codartfor from codalt ";
$Query .= " where codicearti ='" . $db->getField(codicearti) . "'";
$Query .= " and codclifor ='" . $db->getField(codicecf) . "'";
if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
if (!$db2->EOF)
{
  $codartfor = $db2->getField(codartfor);
}
else
{
  $codartfor = "&nbsp;";
}

// scrittura pagina
headx($db->getField(tipodoc) . $db->getField(numerodoc) . $db->getField(codicearti) . trim($db->getField(descrizion)) );

print("<table border=\"1\" >\n");
print ("<tr>\n<th>" . trim($db->getField(tipodoc)) . $db->getField(numerodoc) . "</th>\n");
print ("<th>" .$db->getField(codicecf) . "&nbsp;" . trim($db->getField(ragionesoc)) . "</th>\n</tr>\n");
print ("<tr>\n<td><input type=\"text\" id=\"chkcodice\" size=\"20\" onblur=\"this.value = checkCodiceArti(this.value);\"/></td>\n");
print ("<td>&nbsp;<input type=\"hidden\" id=\"reqcodice\" size=\"20\" value=\"" . trim($db->getField(codicearti)) . "\"/></td>\n</tr>\n");
print ("<tr>\n<td>". $db->getField(codicearti) ."</td>\n<td>" . trim($db->getField(descrizion)) . "</td>\n</tr>\n");
if($db->getField(quantitare) != $db->getField(quantitarenew))
{
  print ("<tr>\n<td class=\"alert\">ATTENZIONE:</td>\n<td class=\"alert\">Residuo modificato!</td>\n</tr>\n");
  print ("<tr>\n<td>Residuo " . $db->getField(unmisura) ."</td>\n<td>" . $db->getField(quantitarenew) . "</td>\n</tr>\n");
}
else
{
  print ("<tr>\n<td>Residuo " . $db->getField(unmisura) ."</td>\n<td>" . $db->getField(quantitare) . "</td>\n</tr>\n");
}
print ("<tr>\n<td>Giacenza " . $db->getField(unmisura) ."</td>\n<td>" . $giacenza . "</td>\n</tr>\n");
print ("<tr>\n<td>Consegna</td>\n<td>" . $db->getField(dataconseg) . "</td>\n</tr>\n");
print ("<tr>\n<td>Ubicazione</td>\n<td>&nbsp;" . $db->getField(ubicazione) . "</td>\n</tr>\n");
print ("<tr>\n<td>Prelevato</td>\n");
if ( $db->getField(picked) == 0 )
{
  $pezzi = $db->getField(quantitarenew);
  $checkval = '';
}
else
{
  $pezzi = $db->getField(picked);
  $checkval = 'checked';
}

print ("<td>\n<input type=\"text\" id=\"quantita\" size=\"5\" value=\"$pezzi\"/>\n");
print ("<input type=\"submit\" value=\"Ok\" onclick=\"sendpickCheck();\"/>\n");
print ("<input type=\"checkbox\" id=\"check\" $checkval />\n</td>\n</tr>\n");
print ("</table>\n<br/>\n");

// campo con id di riga documento per passare i dati al file dei prelievi
print ("<input type=\"hidden\" id=\"idriga\" value=\"$id\"/>\n");

// campi per la navigazione
print ("<table border=\"0\" width=\"100%\" ><tr>\n");
if($prev != 0) {
  print ("<td align=\"left\" width=\"33%\">\n<a href=\"righe-art.php?id=$prev&art=" . urlencode($articolo) . "&user=$user&reparto=$reparto\">\n<img noborder src=\"b_prevpage.gif\"/>Precedente\n</a>\n</td>\n"); }
else {
  print ("<td width=\"33%\">&nbsp;</td>\n"); }
print ("<td width=\"33%\" align=\"center\">\n<a href=\"listaart.php?user=$user&reparto=$reparto\">\n<img noborder src=\"b_home.gif\"/>Altro articolo\n</a>\n</td>\n");

// per sapere se ci sono altre righe devo cercare di leggere la successiva)
$db->MoveNext();
if(!$db->EOF) {
  $link = "righe-art.php?id=" . $db->getField(id) . '&art=' . urlencode($articolo) . "&user=$user&reparto=$reparto" ;
  print ("<td align=\"right\">\n<a href=\"$link\">\nSuccessiva<img noborder src=\"b_nextpage.gif\"/>\n</a>\n</td>\n");
  print ("<input type=\"hidden\" id=\"idnextriga\" value=\"$link\"/>\n"); }
else {
  print ("<td>&nbsp;</td>\n");
  print ("<input type=\"hidden\" id=\"idnextriga\" value=\"0\"/>"); }
print ("</tr></table>\n");


footer();

?>
