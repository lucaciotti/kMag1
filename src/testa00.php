<?php 
/************************************************************************/
/* Project ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2008 by Roberto Ceccarelli                             */
/* */
/************************************************************************/

include("header.php");
$numerodoc = $_GET['numerodoc'];

head("AIR - Testata prebolla");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "Select doctes.numerodoc, doctes.datadoc, doctes.codicecf, doctes.id,";
$Query .= " anagrafe.descrizion"; 
$Query .= " from doctes inner join anagrafe on anagrafe.codice = doctes.codicecf" ;
$Query .= " where tipodoc='00'";
$Query .= " and alltrim(numerodoc)='" . $numerodoc ."'";
$Query .= " and val(esercizio)='" . current_year() ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();


print('<table>');
print('<tr>');
print('<td>Numero</td>');
print('<td>'. $db->getField(numerodoc) . '</td>');
print('</tr>');
print('<tr>');
print('<td>Data</td>');
print('<td>'. $db->getField(datadoc) . '</td>');
print('</tr>');
print('<tr>');
print('<td>Cliente</td>');
print('<td>'. $db->getField(codicecf) . ' - ' . $db->getField(descrizion) . '</td>');
print('</tr>');

print('</table>');

print('<form name="nome" action="righe00.php" method="get">');
print('<input type="hidden" name="id" value="' . $db->getField(id) . '">');
print('<input type="submit" value="Esegui controllo">');
print('</form>');

footer();
?>