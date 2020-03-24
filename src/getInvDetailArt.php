<?php

include("header.php");
//include("odbcSocketLib.php");

$dbInv = getODBCSocket();

$articolo = $_GET['articolo'];
$maga = $_GET['maga'];
$lotto = $_GET['lotto'];

$Query = "Select descrizion, ";
$Query .= "unmisura ";
$Query .= "from magart where codice = '$articolo'";

if (!$dbInv->Execute($Query)) {
    print($dbInv->errorMsg() . "$Query<br>");
    return -1;
}

$descrizion=trim($dbInv->getField(descrizion));
$um=trim($dbInv->getField(unmisura));

print($articolo." <br> ".$descrizion);
print("<table>\n");
print("<tbody >\n");
	/*print ("<tr>\n");
	print ("<th>$articolo</th>\n");
	print ("<th></th>");
	print ("</tr>");
	print ("<tr>\n");
	print ("<th><span style=\"font-size: 9pt;\">" . $descrizion . "</span></th>\n");
	print ("<th></th>");
	print("</tr>\n");*/

	print ("<tr>\n");
	print ("<th>Lotto</th>\n");
	print ("<th>$lotto</th>\n");
	print ("</tr>\n");


	$Query = "Select quantita from u_invent where codicearti = '$articolo' and magazzino='$maga' and lotto='$lotto'";
	if (!$dbInv->Execute($Query)) {
	    print($dbInv->errorMsg() . "$Query<br>");
	    return -1;
	}
	$giac = 0;
	if (!$dbInv->EOF)	{
		$giac = $dbInv->getField(quantita);
		$exist = "";
		$checked = "checked = \"checked\"";
	}
	print ("<tr>\n");
	print ("<th><label for=\"qtaold\">Q.ta inventariata</label></th>\n");
	print ("<th align=\"center\"><b>$giac &nbsp $um</b></th>\n");
	print ("</tr>\n");
print("</tbody>\n</table>\n");

?>