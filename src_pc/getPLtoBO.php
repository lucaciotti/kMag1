<?php

include("header.php");
include("odbcSocketLib.php");

$dbBO = getODBCSocket();

$cId = $_GET['id'];

$Query = "SELECT DISTINCT docrig.tipodoc, docrig.numerodoc from docrig where docrig.tipodoc!='PL' AND docrig.riffromt==$cId";
if (!$dbBO->Execute($Query)) {
    print($dbBO->errorMsg() . "$Query<br>");
    return -1;
}
$entrato = false;
while(!$dbBO->EOF) {
	if(!$entrato){
		$tipo = $dbBO->getField(tipodoc);
		$bo = $dbBO->getField(numerodoc);
		$entrato = true;
	} else {
		$bo .= " - " .$dbBO->getField(numerodoc);
	}
	$dbBO->MoveNext();
}

print("<div><table>\n");
print("<thead>\n");
	print("<tr>");
	    print("<th style='width: 60px;'> Tipo Doc.</th>");
	    print("<th style='width: 40px;'> n° Doc.</th>");
    print("</tr>\n");
print("</thead>\n");

print("<tbody >\n");
print("<tr>\n");

print("<td style='text-align: center;'>".$tipo."</td>");
print("<td style='text-align: center;'>".$bo."</td>");

print("</tr>\n");
print("</tbody>\n</table>\n</div>");

?>