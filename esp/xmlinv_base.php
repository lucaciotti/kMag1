<?php 
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
header("Connection: Keep-Alive"); 
header("Keep-Alive: timeout=300");  
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php"); 
$maga = trim($_GET['maga']);
$anno = current_year();
if(date('n') <4) {
	$anno--;
}

header("Content-Disposition: attachment; filename=\"inventario-$maga-$anno.xml\"");
print("<?xml version=\"1.0\" encoding=\"Windows-1252\"?>");

$str1 = <<<EOT
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>roberto</Author>
  <LastAuthor>roberto</LastAuthor>
  <Created>2012-11-20T13:44:30Z</Created>
  <LastSaved>2012-11-20T13:45:33Z</LastSaved>
  <Company>Kronakoblenz</Company>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>8325</WindowHeight>
  <WindowWidth>14220</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>135</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s62">
   <NumberFormat ss:Format="Fixed"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Inventario">
EOT;

$str2 = <<<EOT
   <Column ss:AutoFitWidth="0" ss:Width="66"/>
   <Column ss:Width="116.25"/>
   <Column ss:Width="282.75"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="79.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="116.25"/>
   <Row ss:AutoFitHeight="0">
    <Cell><Data ss:Type="String">Magazzino</Data></Cell>
    <Cell><Data ss:Type="String">Codice</Data></Cell>
    <Cell><Data ss:Type="String">Descrizione</Data></Cell>
    <Cell><Data ss:Type="String">Quantita</Data></Cell>
    <Cell><Data ss:Type="String">Lotto</Data></Cell>
   </Row>
EOT;

$str3 = <<<EOT
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Unsynced/>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>0</ActiveRow>
     <ActiveCol>0</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
EOT;

$db = getODBCSocket();
$db2 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$out = "";
$Query = "select maggiac.articolo, magart.descrizion, magart.lotti ";
$Query .= "from maggiac inner join magart on magart.codice = maggiac.articolo ";
$Query .= "where maggiac.magazzino == '".$maga."' and maggiac.esercizio == '".$anno."'";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
$row = 1;
while(!$db->EOF) {
	$art = $db->getField(articolo);
	$desc = $db->getField(descrizion);
	if($db->getField(lotti)) {
		$Query = "select lotto from maggiacl where magazzino = '".$maga."' and articolo = '".$art."'";
		$Query .= "and progqtacar+progqtaret-progqtasca > 0";
		if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>";
		while(!$db2->EOF) {
			$row++;
			$out .= writeRow($maga, $art, $desc, $db2->getField(lotto));
			$db2->MoveNext();
		}
	} else {
		$row++;
		$out .= writeRow($maga, $art, $desc, "");
	}
	$db->MoveNext();
} 
$strn = "<Table ss:ExpandedColumnCount=\"5\" ss:ExpandedRowCount=\"$row\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultRowHeight=\"15\">\n";
   
print($str1 . $strn . $str2 . $out . $str3); 

function writeRow($maga, $art, $desc, $lotto) {
	$desc = xmlentities($desc);
	$art = xmlentities($art);
	$lotto = xmlentities($lotto);
	$out = "<Row ss:AutoFitHeight=\"0\">\n";
	$out .= "<Cell><Data ss:Type=\"String\">$maga</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$art</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$desc</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"Number\">0</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$lotto</Data></Cell>\n";
	$out .= "</Row>\n";
	return $out;
}

function xmlentities($string) {
    return str_replace(array("&", "<", ">", "\"", "'", "“", "”"),
        array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&quot;", "&quot;"), $string);
}
?>