<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Caricamento inventario da Excel");
?>
<script type="text/javascript">
//<![CDATA[
var downloadURL = function downloadURL(url) {
    var iframe;
    var hiddenIFrameID = 'hiddenDownloader';
    iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');  
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;   
};

var getxml = function() {
	var maga = document.getElementById("maga").value;
	downloadURL("xmlinv_base.php?maga="+maga);
};
//]]>
</script>
<?php

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$Query = "select codice, descrizion from magana order by codice";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

print("<select id=\"maga\">\n");
while(!$db->EOF) {
	$codice = $db->getField(codice);
	$descrizion = $db->getField(descrizion);
	print("<option value=\"$codice\">$codice - $descrizion</option>\n");
	$db->MoveNext();
} 
print("</select>\n");

print("&nbsp;<input type=\"button\" value=\"Scarica il foglio da compilare\" onclick=\"getxml();\">\n");
print("<br><br><br>\n");

print("<form action=\"xml2inv.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
print("<label for=\"file\">Filename:</label>\n");
print("<input type=\"file\" name=\"file\" id=\"file\" accept=\"text/xml\">\n"); 
print("&nbsp;<input type=\"submit\" id=\"btnok\" value=\"Carica il foglio compilato\" >\n");
print("</form>\n");

print("<br>\n");
goMain();
footer();
?>