<?php 
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$id = $_GET['id'];
$id_testa = $_GET['idtesta'];
$user = $_GET['user'];
$mode = $_GET['mode'];
$doc = isset($_GET['doc']) ? $_GET['doc'] : 0;
$anno = current_year();

$db = getODBCSocket();
$db2 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select docrig.codicearti, docrig.descrizion, u_picklist.unmisura, u_picklist.quantitare, u_picklist.dataconseg, u_picklist.codicecf, ";
$Query .= " u_picklist.tipodoc, u_picklist.numerodoc, u_picklist.id, magart.ubicazione,"; 
$Query .= " docrig.quantitare as quantitarenew, docrig.note, docrig.id as id_docrig, "; 
$Query .= " u_pick.quantita as picked";
$Query .= " from docrig left outer join u_picklist on docrig.id = u_picklist.id" ;
$Query .= " left outer join magart on magart.codice = u_picklist.codicearti" ;
$Query .= " left outer join u_pick on u_pick.id_riga = u_picklist.id ";
$Query .= " where docrig.id_testa = $id_testa";
$Query .= " order by docrig.numeroriga";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

// ho cercato tutte le righe di ordine perche' mi serviranno per conoscere precedente e successiva
//adesso devo fare lo scan per posizionarmi sulla riga richiesta
$prev = 0;
while($db->getField(id_docrig) != $id) {
  $prev = $db->getField(id_docrig);
  $db->MoveNext();
}

// Lettura giacenze
$Query = "Select giacini+progqtacar-progqtasca as giacenza from maggiac";
$Query .= " where magazzino='" . CONFIG::$DEFAULT_MAG . "' and esercizio='" . $anno . "' and articolo = '" . $db->getField(codicearti) ."'"; 
if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
if (!$db2->EOF)
{
  $giacenza = $db2->getField(giacenza);	
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

// carico un iframe nascosto per gestire le ubicazioni aggiuntive
print("<script type=\"text/javascript\" src=\"csspopup.js\"></script>\n");
print("<div id=\"ubiclist\" style=\"display: none\">\n");
$Query = "select * from u_ubicaz ";
$Query .= "where codicearti = '" . $db->getField(codicearti) . "' ";
$Query .= "order by idprog ";
if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
print("<table><tr><th>N.</th><th>Ubicaz.</th></tr>\n");
for($j=0; $j<10; $j++) {
  print("<tr><td>$j</td><td><input type=\"text\" name=\"ub$j\" id=\"ub$j\" size=\"8\" onblur=\"writeUbicaz($j);\" ");
  if(!$db2->EOF) {
    if($db2->getField(idprog) == $j) {
	  print("value=\"" . $db2->getField(ubicazione) . "\"");
	  $db2->MoveNext();
	}
  }
  print("></td></tr>\n");
}
//print("<img src=\"wip.jpg\">");
print("</table>");
print("&nbsp;&nbsp;<input type=\"submit\" value=\"Chiudi\" onclick=\"popup('ubiclist',-1);\">");
print("</div>\n");

$row_id = $db->getField(id);
print("<form method=\"get\" id=\"sendpick\" action=\"writepick.php\">");
print("<table border=\"1\" >\n");
if( !empty($row_id)) {
  print("<tr>\n<th>" . trim($db->getField(codicearti)) );
  print("\n<input type=\"hidden\" id=\"reqcodice\" size=\"20\" value=\"" . trim($db->getField(codicearti)) . "\"/>\n"); 
  print("</th>\n<th>" . trim($db->getField(descrizion)) . "</th>\n</tr>\n"); 
  if( stripos($mode,'f') === false) {
    print ("<tr>\n<td><input type=\"text\" id=\"chkcodice\" size=\"20\" onblur=\"this.value = checkCodiceArti(this.value);\"/></td>\n");
    print ("<td>&nbsp;</td>\n</tr>\n"); 
  }
  if( $db->getField(note) != "") {
    print ("<tr>\n<td>Note</td>\n<td>" . $db->getField(note) . "</td>\n</tr>\n"); 
  }
  print ("<tr>\n<td>Cod.Art. Cli/For</td>\n<td>" . $codartfor . "</td>\n</tr>\n"); 
  if($db->getField(quantitare) != $db->getField(quantitarenew)) {
    print ("<tr>\n<td class=\"alert\">ATTENZIONE:</td>\n<td class=\"alert\">Residuo modificato!</td>\n</tr>\n");
    print ("<tr>\n<td>Residuo " . $db->getField(unmisura) ."</td>\n<td>" . $db->getField(quantitarenew) . "</td>\n</tr>\n");
  } else {
    print ("<tr>\n<td>Residuo " . $db->getField(unmisura) ."</td>\n<td>" . $db->getField(quantitare) . "</td>\n</tr>\n");
  }  
  print ("<tr>\n<td>Giacenza " . $db->getField(unmisura) ."</td>\n<td>" . $giacenza . "</td>\n</tr>\n");
  print ("<tr>\n<td>Consegna</td>\n<td>" . $db->getField(dataconseg) . "</td>\n</tr>\n"); 
  print ("<tr>\n<td>Ubicazione</td>\n"); 
  if( stripos($mode,'f') === false) {
    print ("<td>&nbsp;" . $db->getField(ubicazione)); 
  } else {
    print ("<td><input type=\"text\" id=\"ubicazione\" size=\"5\" value=\"" . $db->getField(ubicazione) . "\" onblur=\"sendUbicazione();\" />\n");
    print("&nbsp;&nbsp;<input type=\"button\" value=\"Altre ub.\" onclick=\"popup('ubiclist',-1);\">");
  }  
  print ("</td>\n</tr>\n"); 
  if( stripos($mode,'f') === false) {
    print ("<tr>\n<td>Prelevato</td>\n");
  } else {
    print ("<tr>\n<td>Caricato</td>\n");
  }  
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

  print ("<td><input type=\"text\" id=\"quantita\" name=\"quantita\" size=\"5\" value=\"$pezzi\"/>\n");
  print ("<input type=\"submit\" value=\"Ok\">\n");
  if ($checkval=='checked'){
    print ("<input type=\"checkbox\" id=\"check\" $checkval /> Prelevati");
  }
  print("</td>\n</tr>\n");
} else {
  print("<tr>\n<th>" . trim($db->getField(codicearti)) );
  print("</th>\n<th>" . trim($db->getField(descrizion)) . "</th>\n</tr>\n"); 
  if( $db->getField(note) != "") {
    print ("<tr>\n<td>Note</td>\n<td>" . $db->getField(note) . "</td>\n</tr>\n"); 
  }
} 
print ("</table>\n<br/>\n");
print("<input type=\"hidden\" id=\"idtesta\" name=\"idtesta\" value=\"$id_testa\">\n");
print("<input type=\"hidden\" id=\"id\" name=\"id\" value=\"$id\">\n");
print("<input type=\"hidden\" id=\"user\" name=\"user\" value=\"$user\">\n");
print("<input type=\"hidden\" id=\"mode\" name=\"mode\" value=\"$mode\">\n");
print("</form>\n");

/* campo con id di riga documento per passare i dati al file dei prelievi */
print ('<input type="hidden" id="idriga" value="' . $id .'"/>');

/* campi per la navigazione */
print ("<table border=\"0\" width=\"100%\" >\n<tr>\n");
if($prev != 0) {
  print ("<td align=\"left\" width=\"33%\"><a href=\"righe-lista.php?id=$prev&idtesta=$id_testa&user=$user&mode=$mode&doc=$doc\"><img noborder src=\"b_prevpage.gif\"/>Precedente</a></td>\n"); }
else { 
  print ("<td width=\"33%\">&nbsp;</td>\n"); }
print ("<td width=\"33%\" align=\"center\"><a href=\"testadoc-lista.php?id=$id_testa&user=$user&mode=$mode&doc=$doc\"><img noborder src=\"b_home.gif\"/>Testa documento</a></td>\n");

/* per sapere se ci sono altre righe devo cercare di leggere la successiva) */
$db->MoveNext();
if(!$db->EOF) {
  $link = "righe-lista.php?id=" . $db->getField(id_docrig) . "&idtesta=$id_testa&user=$user&mode=$mode&doc=$doc" ;
  print ("<td align=\"right\"><a href=\"$link\">Successiva<img noborder src=\"b_nextpage.gif\"/></a>\n"); 
  print ("<input type=\"hidden\" id=\"idnextriga\" value=\"$link\"/></td>\n"); }
else { 
  print ("<td>&nbsp;"); 
  print ("<input type=\"hidden\" id=\"idnextriga\" value=\"0\"/></td>\n"); }
print ("</tr>\n<tr>\n<td>&nbsp;</td>\n<td align=\"center\">");
if(stripos($mode,"b") === false) {
  print("<a href=\"lista.php?user=$user\">");
} else {
    if($doc){
        print("<a href=\"getfordoc.php\">");
    }
    else{
        print("<a href=\"getdoc.php\">");
    }
}
print("<img noborder src=\"b_search.gif\"/>Altra ricerca</A></td>\n<td>&nbsp;</td>\n");
print ("</tr>\n</table>\n");



footer();
?>