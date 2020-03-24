<?php 
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");

$cCodice = $_GET['cod'];
$cCF = $_GET['cf'];

$dbtest = getODBCSocket();

/* cerchiamo se esiste un articolo con il codice richiesto */

$Query = "Select codice from magart where codice ='".$cCodice."'";
if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if ($dbtest->EOF)
{
  // Non abbiamo trovato l'articolo, vediamo se e' un alias
  $Query = "select codicearti from magalias where alias = '".$cCodice."'";
  if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
  if ($dbtest->EOF)
  {
	  // Non abbiamo trovato nemmeno l'alias
		//provo con il codalt
	  $Query = "select codicearti from codalt where u_barcode = '".$cCodice."'";
	  if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
	  if ($dbtest->EOF)
	  {
	    // Non abbiamo trovato nemmeno l'alias
	    $return = "*error*";	
	  }
	  else
	  {
	    $return = (trim($dbtest->getField(codicearti)));
	  }	
  }
  else
  {
    $return = (trim($dbtest->getField(codicearti)));
  }
}
else
{
  $return = (trim($cCodice));
}
$unmisura = "";
$out = "<artinfo>";
$out .= "<codice>$return</codice>";
if($return != "*error*") {
  $Query = "Select descrizion,ubicazione,u_reparto,lotti,unmisura,danger,u_misural,u_misurah,u_misuras,pesounit, ";
  $Query .= "unmisura1,unmisura2,unmisura3,fatt1,fatt2,fatt3 ";
  $Query .= "from magart where codice ='".$return."'";
  if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
  if("" == $unmisura) {
	$unmisura = (trim($dbtest->getField(unmisura)));
  }
  $out .= "<descrizion>" . trim($dbtest->getField(descrizion)) . "</descrizion>";
  $out .= "<ubicazione>" . trim($dbtest->getField(ubicazione)) . "</ubicazione>";
  $out .= "<reparto>" . trim($dbtest->getField(u_reparto)) . "</reparto>";
  $out .= "<unmisura>$unmisura</unmisura>";
  $out .= "<unmisura1>" . trim($dbtest->getField(unmisura1)). "</unmisura1>";
  $out .= "<unmisura2>" . trim($dbtest->getField(unmisura2)) . "</unmisura2>";
  $out .= "<unmisura3>" . trim($dbtest->getField(unmisura3)) . "</unmisura3>";
  $out .= "<fatt1>" . $dbtest->getField(fatt1) . "</fatt1>";
  $out .= "<fatt2>" . $dbtest->getField(fatt2) . "</fatt2>";
  $out .= "<fatt3>" . $dbtest->getField(fatt3). "</fatt3>";
  $out .= "<u_misural>" . $dbtest->getField(u_misural) . "</u_misural>";
  $out .= "<u_misurah>" . $dbtest->getField(u_misurah) . "</u_misurah>";
  $out .= "<u_misuras>" . $dbtest->getField(u_misuras) . "</u_misuras>";
  $out .= "<pesounit>" . $dbtest->getField(peso) . "</pesounit>";
  $out .= "<imballo>" . ($dbtest->getField(danger) ? 1 : 0) . "</imballo>";
  $out .= "<lottoob>" . ($dbtest->getField(lotti) ? 1 : 0) . "</lottoob>";
  $out .= "<lotti>";
  $Query = "select codice from lotti where codicearti ='".$return."' order by codice desc";
  if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
  while(!$dbtest->EOF && trim($dbtest->getField(codice))!="") {
	$out .= "<lotto>" . trim($dbtest->getField(codice)) . "</lotto>";
	$dbtest->MoveNext();
  }
  $out .= "</lotti>";
  $Query = "select codice from u_pallet where codice ='".$return."'";
  if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
  $out .= "<pallet>" . ($dbtest->EOF ? 0 : 1) . "</pallet>";
}

$out .= "</artinfo>";

print($out); 


/*diconnect from database 
$rs->Close();
$conn->Close();
$rs = null;
$conn = null;*/
 
?>