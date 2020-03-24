<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Ricerca PL");
disableCR();
?>
<style>
label	{float: left; width: 100px;}
body	{width: 350px;}
table, td, tr 	{border-style: none; border-collapse: collapse;}
</style>
<script type="text/javascript">
//<![CDATA[
var checkCodice = function(cCodice) {
	var oRet = new Object();
	var url = "getrigadocx.php?cod=" + encodeURIComponent(cCodice);
	var milliseconds = (new Date()).getTime();
	url += "&ms=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	oRet.codice = xRet.getElementsByTagName("codicearti")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		alert("Codice non riconosciuto");
		oRet.codice = "";
		oRet.numero = "";
		oRet.esercizio = "";
		oRet.id_testa = 0;
	} else {
		oRet.numero = xRet.getElementsByTagName("numerodoc")[0].firstChild.nodeValue;
		oRet.esercizio = xRet.getElementsByTagName("esercizio")[0].firstChild.nodeValue;
		oRet.id_testa = xRet.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
	}
	return oRet;
};

var decode = function(obj) {
  var x = checkCodice(obj.value);
  document.getElementById('num').value = x.numero;
  document.getElementById('anno').value = x.esercizio;
  document.getElementById('id_testa').value = x.id_testa;
};
//]]>
</script>
<label for="id">ID Articolo:</label> 
<input type="text" name="id" id="id" onblur="decode(this);">
<hr>
<form name="input" action="pl-banc.php" method="get">
<label for="num">PL numero:</label> 
<input type="text" name="num" id="num" size="4"><br>
<label for="anno">Anno:</label> 
<input type="text" name="anno" id="anno" size="4">
<hr>
<!--
<label for="rep" style="width: 200px;">Raggruppa per reparto</label> 
<input type="checkbox" id="rep" name="rep" value="rep" checked="checked"><br><br>
-->
<table style="width: 100%;"><tr>
<td style="width: 33%;">
<input type="submit" name="btnok" id="btnok" value="Colli">
</td>
<td style="width: 33%;">
<input type="submit" name="gestbanc" id="gestbanc" value="Bancali">
</td>
<td>
<input type="submit" name="art" id="art" value="Articoli">
</td>
</tr></table>
<input type="hidden" id="id_testa" name="id_testa" value="0">
</form>
<script type="text/javascript">
//<![CDATA[
document.getElementById('anno').value = (new Date()).getFullYear();
//]]>
</script>
<?php
setFocus("id");
goMain();
footer();
?>